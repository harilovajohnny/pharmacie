<?php

namespace App\Repositories\Front;

use App\{
    Models\User,
    Models\Setting,
    Helpers\EmailHelper,
    Models\Notification
};
use App\Helpers\ImageHelper;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class UserRepository
{

    public function register($request){
        $input = $request->all();
        // dd($input);
        $user = new User;
        $input['password'] = bcrypt($request['password']);
        $input['email'] = $input['email'];
        $input['first_name'] = $input['first_name'];
        $input['last_name'] = $input['last_name'];
        $input['phone'] = $input['phone'];
        $input['gender'] = $input['gender'];
        $input['date_birth'] = $input['date_birth'];
        $input['ship_zip'] = $input['ship_zip'];
        $input['ship_city'] = $input['city'];
        $input['bill_country'] = $input['bill_country'];
        $input['ship_address1'] = $input['adresse'];
        $verify = Str::random(6);
        $input['email_token'] = $verify;
        $user->fill($input)->save();

        Notification::create(['user_id' => $user->id]);

        $emailData = [
            'to' => $user->email,
            'type' => "Registration",
            'user_name' => $user->displayName(),
            'order_cost' => '',
            'transaction_number' => '',
            'site_title' => Setting::first()->title,
        ];

        $email = new EmailHelper();
        $email->sendTemplateMail($emailData);

        $users = User::find($user->id);

        // if (Auth::attempt(['email' => "User_3@gmail.com", 'password' => "AZ123456"])) {

        //     // if successful, then redirect to their intended location
        //     if(!Auth::user()->email_token){
        //       Session::flash('error',__('Email not verify !'));
        //       return redirect()->back();
        //     }
        //       if($request->has('modal')){
        //         return redirect()->back();
        //       }else{
        //         return redirect()->intended(route('user.dashboard'));
        //       }
        // }

        // dd($users);
        // return $users;
    }





    public function profileUpdate($request){
        $input = $request->all();
        if($request['user_id']){
            $user = User::findOrFail($request['user_id']);
        }else{
            $user = Auth::user();
        }


        if($request->password){
            $input['password'] = bcrypt($input['password']);
            $user->password = $input['password'];
            $user->update();
        }else{
            unset($input['password']);
        }

      
        if ($file = $request->file('photo')) {
            $input['photo'] = ImageHelper::handleUpdatedUploadedImage($file,'/assets/images',$user,'/assets/images/','photo');
        }

        if($request->newsletter){
            if(!Subscriber::where('email',$user->email)->exists()){
                Subscriber::insert([
                    'email' => $user->email
                ]);
            }
        }else{
            Subscriber::where('email',$user->email)->delete();
        }

        $user->fill($input)->save();
    }




}
