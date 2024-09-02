<?php

namespace App\Http\Controllers;
use App\Mail\MailNotify;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
class MailController extends Controller
{
    public function mailNotify(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email:accounts'
        ]);
        $account=Account::where('email',$request->email)->first();
        if($validator->fails() or !($account)){
            return new JsonResponse(
                [
                    'success' => false, 
                    'message' => $validator->errors()
                ], 422
            );
        }
        
       
        else
        {Mail::to($account->email)->send(new MailNotify($account->email));
            return new JsonResponse(
                [
                    'success' => true,
                    'message' => "Thank you for subscribing to our email, please check your inbox"
                ], 200
            );}
        }
    
}
