<?php

namespace App\Http\Middleware;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{

    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::check() && (Auth::user()->user_type='Super Admin') ) {
                return redirect('superadmin/dashboard');
            }elseif (Auth::check() && (Auth::user()->user_type == 'Admin') ) {
                return redirect('admin/dashboard');
            }elseif (Auth::check() && (Auth::user()->user_type == 'Salesman') ) {
                return redirect('salesman/dashboard');
            }elseif (Auth::check() && (Auth::user()->user_type == 'Agent') ) {
                return redirect('agent/dashboard');
            }elseif (Auth::check() && (Auth::user()->user_type == 'Company') ) {
                return redirect('company/dashboard');
            }else{
                return $next($request);
            }
        }

        return $next($request);


    }
}
