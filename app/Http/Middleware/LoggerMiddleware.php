<?php

namespace App\Http\Middleware;

use Closure;
use  App\Models\User;
use Illuminate\Http\Request;

class LoggerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
         $user=User::where('ipaddress',$request->ip())->first();
        if(!$user){
            $user_new=new User();
            $user_new->ipaddress=$request->ip();
            $user_new->visitor_count +=1;
            $user_new->save();
        }
        else{
          $user->visitor_count +=1; 
          $user->save();
        }
        return $next($request);
    }
}
