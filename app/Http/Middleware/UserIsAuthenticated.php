<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserIsAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $allowedType): Response
    {

        if(!$request->session()->has('user_id')){
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }

        
        $userType = $request->session()->get('usertype');
   
        if((string) $userType !== (string) $allowedType){
            switch ($userType) {
                case 0:
                    return redirect()->route('requestForm')->with('error', 'Access Denied. Redirected to Requestor.');
                case 1:
                    return redirect()->route('dashboard')->with('error', 'Access Denied. Redirected to Admin.');
                case 2:
                    return redirect()->route('technician.request')->with('error', 'Access Denied. Redirected to Technician.');
                default:
                    return redirect()->route('login')->with('error', 'Invalid user type.');
            }
        }

        return $next($request);
    }
}
