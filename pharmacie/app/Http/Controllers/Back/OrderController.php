<?php

namespace App\Http\Controllers\Back;

use App\{
    Models\Order,
    Models\PromoCode,
    Models\TrackOrder,
    Http\Controllers\Controller
};
use App\Helpers\PriceHelper;
use App\Helpers\SmsHelper;
use App\Mail\CancelledMail;
use App\Mail\LivraisonMail;
use App\Mail\PendingMail;
use App\Mail\ValidationMail;
use App\Models\Doctor_information;
use App\Models\Livraison_information;
use App\Models\Notification;
use App\Models\User_info_medical;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{

    /**
     * Constructor Method.
     *
     * Setting Authentication
     *
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->type){
           
            if($request->start_date && $request->end_date){
                $datas = $start_date = Carbon::parse($request->start_date);
                $end_date = Carbon::parse($request->end_date);
                $datas = Order::latest('id')->whereOrderStatus($request->type)->whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date)->get();
            }else{
                $datas = Order::latest('id')->whereOrderStatus($request->type)->get();
            }
            
        }else{

            if($request->statut_paiement){

                $datas = Order::where('payment_status',$request->statut_paiement)->get();
            }
            else if($request->start_date && $request->end_date){
                $datas = $start_date = Carbon::parse($request->start_date);
                $end_date = Carbon::parse($request->end_date);
                $datas = Order::latest('id')->whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date)->get();
            }else if($request->user_name){
                $nom = $request->user_name;
                $datas = Order::whereHas('user', function ($query) use ($nom) {
                    $query->where('first_name', 'LIKE', "%$nom%");
                })->get();

            }else if($request->user_email){
                $email = $request->user_email;
                $datas = Order::whereHas('user', function ($query) use ($email) {
                    $query->where('email', 'LIKE', "%$email%");
                })->get();
                
            }else if($request->order_date){
                $order_dates = Carbon::parse($request->order_date);
                $datas = Order::latest('id')->whereDate('created_at','=',$order_dates)->get();
            }
            else if($request->order_id){
                $id = $request->order_id;
                $datas = Order::latest('id')->where('id','=',$id)->get();
            }
            
            // else if($request->user_phone){
            //     $phone = $request->user_phone;
            //     $datas = Order::whereHas('user', function ($query) use ($phone) {
            //         $query->where('phone', 'LIKE', "%$phone%");
            //     })->get();
            // }
            
            else{
                $datas = Order::latest('id')->get();
            }
        }
    
        return view('back.order.index',compact('datas'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invoice($id)
    {
        $order = Order::findOrfail($id);
        $cart = json_decode($order->cart, true);
        $user_info = User_info_medical::where('order_id',$id)->first();
        $doctor_info = Doctor_information::where('order_id',$id)->first();
        
        return view('back.order.invoice',compact('order','cart','user_info','doctor_info'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printOrder($id)
    {
        $order = Order::findOrfail($id);
        $cart = json_decode($order->cart, true);
       
        return view('back.order.print',compact('order','cart'));
    }


    /**
     * Change the status for editing the specified resource.
     *
     * @param  int  $id
     * @param  string  $field
     * @param  string  $value
     * @return \Illuminate\Http\Response
     */
    public function status($id,$field,$value)
    {
        $order = Order::find($id);

        if($field == 'payment_status'){
            if($order['payment_status'] == 'Paid'){
                return redirect()->route('back.order.index')->withErrors(__('Order is already paid.'));
            }
        }
        if($field == 'order_status'){
            if($order['order_status'] == 'Delivered'){
                return redirect()->route('back.order.index')->withErrors(__('Order is already Delivered.'));
            }
        }
        $order->update([$field => $value]);
        if($order->payment_status == 'Paid'){
            $this->setPromoCode($order);
        }

        $id_livraison =null;

        $this->setTrackOrder($order,$id_livraison);
        $sms = new SmsHelper();
        $user_number = $order->user->phone;
        if($user_number){
            $sms->SendSms($user_number,"'order_status'",$order->transaction_number);
        }
       
        return redirect()->route('back.order.index')->withSuccess(__('Status Updated Successfully.'));
    }

    public function status_livraison(Request $request){
        
        $id = $request->input('id_orders');
        $id_livraison = $request->input('id_livraison');
        $order = Order::find($id);


        $livaison = new Livraison_information([
            'order_id' => $id ,
            'value_id' => $id_livraison
        ]);
        $livaison->save();


        $orders_all = json_decode($order->shipping_info,true);

        $total_amount =PriceHelper::OrderTotal($order);
        $orders_all['total_amount'] = $total_amount; 
        $orders_all['livraison_id'] = $id_livraison; 


        //Email pour validation pour client
        Mail::to($orders_all['ship_email'])->send(new LivraisonMail($orders_all));

        if(!TrackOrder::whereOrderId($order->id)->whereTitle('In Progress')->exists()){
            
            TrackOrder::create([
                'title' => 'In Progress',
                'order_id' => $order->id
            ]);
        }

        if(!TrackOrder::whereOrderId($order->id)->whereTitle('Delivered')->exists()){
            TrackOrder::create([
                'title' => 'Delivered',
                'order_id' => $order->id
            ]);
        }


        $sms = new SmsHelper();
        $user_number = $order->user->phone;
        if($user_number){
            $sms->SendSms($user_number,"'order_status'",$order->transaction_number);
        }
       
        return redirect()->route('back.order.index')->withSuccess(__('Status Updated Successfully.'));

    }

    /**
     * Custom Function
     */
    public function setTrackOrder($order,$id_livraison)
    {

        if($order->order_status == 'In Progress'){
            if(!TrackOrder::whereOrderId($order->id)->whereTitle('In Progress')->exists()){

                $orders_all = json_decode($order->shipping_info,true);

                $total_amount =PriceHelper::OrderTotal($order);
                $orders_all['total_amount'] = $total_amount; 

                //Email pour validation pour client
                Mail::to($orders_all['ship_email'])->send(new ValidationMail($orders_all));

                TrackOrder::create([
                    'title' => 'In Progress',
                    'order_id' => $order->id
                ]);
            }
        }


        if($order->order_status == 'Pending'){
            // if(!TrackOrder::whereOrderId($order->id)->whereTitle('In Progress')->exists()){

                $orders_all = json_decode($order->shipping_info,true);

                //Email pour validation pour client
                Mail::to($orders_all['ship_email'])->send(new PendingMail($orders_all));

                TrackOrder::create([
                    'title' => 'Pending',
                    'order_id' => $order->id
                ]);
            // }
        }


        if($order->order_status == 'Canceled'){
            
            $orders_all = json_decode($order->shipping_info,true);
            Mail::to($orders_all['ship_email'])->send(new CancelledMail($orders_all));

            if(!TrackOrder::whereOrderId($order->id)->whereTitle('Canceled')->exists()){

                if(!TrackOrder::whereOrderId($order->id)->whereTitle('In Progress')->exists()){
                    TrackOrder::create([
                        'title' => 'In Progress',
                        'order_id' => $order->id
                    ]);
                }
                if(!TrackOrder::whereOrderId($order->id)->whereTitle('Delivered')->exists()){
                    TrackOrder::create([
                        'title' => 'Delivered',
                        'order_id' => $order->id
                    ]);
                }

                if(!TrackOrder::whereOrderId($order->id)->whereTitle('Canceled')->exists()){
                    TrackOrder::create([
                        'title' => 'Canceled',
                        'order_id' => $order->id
                    ]);
                }


            }
        }


        if($order->order_status == 'Delivered'){

            $orders_all = json_decode($order->shipping_info,true);

            $total_amount =PriceHelper::OrderTotal($order);
            $orders_all['total_amount'] = $total_amount; 
            $orders_all['livraison_id'] = $id_livraison; 


            //Email pour validation pour client
            Mail::to($orders_all['ship_email'])->send(new LivraisonMail($orders_all));

            if(!TrackOrder::whereOrderId($order->id)->whereTitle('In Progress')->exists()){
                
                TrackOrder::create([
                    'title' => 'In Progress',
                    'order_id' => $order->id
                ]);
            }

            if(!TrackOrder::whereOrderId($order->id)->whereTitle('Delivered')->exists()){
                TrackOrder::create([
                    'title' => 'Delivered',
                    'order_id' => $order->id
                ]);
            }
        }
    }


    public function setPromoCode($order)
    {

        $discount = json_decode($order->discount, true);
        if($discount != null){
            $code = PromoCode::find($discount['code']['id']);
            $code->no_of_times--;
            $code->update();
        }
    }

    public function delete($id)
    {
        $order = Order::findOrFail($id);
        Doctor_information::where('order_id',$id)->delete();
        User_info_medical::where('order_id',$id)->delete();
        if(Livraison_information::where('order_id',$id)->exists()){
            Livraison_information::where('order_id',$id)->delete();
        }
        
        $order->tranaction->delete();
        if(Notification::where('order_id',$id)->exists()){
            Notification::where('order_id',$id)->delete();
        }
        if(count($order->tracks_data)>0){
            foreach($order->tracks_data as $track){
                $track->delete();
            }
        }
        $order->delete();
        return redirect()->back()->withSuccess(__('Order Deleted Successfully.'));
    }

}
