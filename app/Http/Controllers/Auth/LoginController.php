<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    protected function redirectTo() {
        if (Auth::check() && (Auth::user()->user_type == 'Super Admin')) {
            return $this->redirectTo = route('superadmin.dashboard');
        }
        elseif (Auth::check() && (Auth::user()->user_type == 'Admin')) {
            return $this->redirectTo = route('admin.dashboard');
        }
        elseif (Auth::check() && (Auth::user()->user_type == 'Salesman')) {
            return $this->redirectTo = route('salesman.dashboard');
        }
        elseif (Auth::check() && (Auth::user()->user_type == 'Agent')) {
            return $this->redirectTo = route('agent.dashboard');
        }
        elseif (Auth::check() && (Auth::user()->user_type == 'Company')) {
            return $this->redirectTo = route('company.dashboard');
        }
        else {
            return('/login');

        }
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function logout(Request $request)
    {
        User::find(Auth::id())->update(array('remember_token' => null));
        Auth::logout();
        $request->session()->invalidate();
        return back();
    }



}
