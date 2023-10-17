<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {


        $user = $request->user() ;

        if(!$user){
           return  redirect()->route('dashboardLogin') ;
        } 

        if($user->role==='user'){
            return abort(403) ;
        }
        return $next($request);



    }
}
