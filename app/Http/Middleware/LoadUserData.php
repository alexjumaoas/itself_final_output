<?php

namespace App\Http\Middleware;

use App\Models\Dtruser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoadUserData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($request->session()->has('user_id')) {
            $userId = $request->session()->get('user_id');
            $user = Dtruser::find($userId);
       
            // Make the user available to all views
            view()->share('userInfo', $user);
            
            // Or attach to the request for controller access
            $request->merge(['currentUser' => $user]);
        }

        return $next($request);
    }
}
