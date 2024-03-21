<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function sendmailadmin(){
        dd('emaisql envoye');
        // $data = ['name' => 'John Doe'];
        // Mail::to('ambalafenofikambanana@gmail.com')->send(new AdminMail($data));
        // dd('email envoye');
    }
}
