<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\MailNotify;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        $users=User::where('ipaddress','!=',null)->get();
        $visitor_count=0;
        foreach($users as $user){
           $visitor_count +=$user->visitor_count;
        }
        return response()->json(['visitor_numbers'=>$visitor_count ],200);
    }
    public function get(){
        $users=User::where('ipaddress','!=',null)->get();
        $visitor_count=0;
        foreach($users as $user){
           $visitor_count +=$user->visitor_count;
        }
        return response()->json(['visitor_numbers'=>$visitor_count ],200);
    }
    public function sendEmail(Request $request){
      
        $email = 'alwasah8@gmail.com';
      
        $data = [
            'userName' => $request->userName,
            'email' => $request->email,
            'phone' => $request->phone,
            'companyName' => $request->companyName,
            'messageTitle' => $request->messageTitle,
            'messageContent' => $request->messageContent,
        ];

       $result= Mail::to($email)->send(new MailNotify($data));

        return response()->json([
            'message' =>'تم إرسال معلوماتك  بالبريد الالكتروني بنجاح']);
   
  
}
}