<?php

namespace App\Http\Controllers;
use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Http\Request;
// use App\Models\UserLogingInfo;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Stevebauman\Location\Facades\Location;

class UserLoginController extends Controller
{
    //check authentication of email or phone and password
    public function login(Request $request)
    {
        // if (is_numeric($request->email)) {
        //     $customerInfo = array("phone" => $request->email, "password" => $request->password, 'status' => 1);
        // } else {
        //     $customerInfo = array("email" => $request->email, "password" => $request->password, 'status' => 1);
        // }
        $customerInfo = array("username" => $request->username, "password" => $request->password, 'status' => 1);
        $auth = Auth::attempt($customerInfo);
        if ($auth) {
            $userId = Auth::id();
            $ip = $request->ip();
            //$userAgent = $request->header('User-Agent');
            // $userInfos = UserLogingInfo::whereuser_id($userId)->whereip_address($ip)->get();
            // if (count($userInfos) <= 5) {
            //     $userInfo = UserLogingInfo::whereuser_id($userId)->whereip_address($ip)->first();
            //     if ($userInfo) {
            //         $save = UserLogingInfo::find($userInfo->id);
            //         $save->log_in = now();
            //         $save->user_agent =  $userAgent;
            //         $save->save();
            //     } else {
            //         $userInfo = new UserLogingInfo();
            //         $userInfo->user_id = $userId;
            //         $userInfo->ip_address = $ip;
            //         $userInfo->log_in = now();
            //         $userInfo->user_agent = $userAgent;
            //         $userInfo->save();
            //     }
            // } else {

            //     $last = UserLogingInfo::whereuser_id($userId)->whereip_address($ip)->last();
            //     $delete = UserLogingInfo::destroy($last->id);
            //     if ($delete) {
            //         $userInfo = new UserLogingInfo();
            //         $userInfo->user_id = $userId;
            //         $userInfo->ip_address = $ip;
            //         $userInfo->login_in = now();
            //         $userInfo->user_agent = $userAgent;
            //         $userInfo->save();
            //     }
            // }
            // $lastLogin = UserLogingInfo::whereuser_id($userId)->get()->last();

            // $currentUserInfo = Location::get($ip);

            $user = User::findOrFail($userId);
            $user->ip_address = $ip;
            $user->last_login = date('Y-m-d H:i:s');
            $user->save();

            // Operation Log Success
            Helper::operationLog(NULL, 'Login','Login',NULL,NULL,'Success',NULL,NULL);

            Toastr::success("Successfully Login", "Success");
            if (Auth::check() && (Auth::user()->user_type == 'Super Admin')) {
                return redirect()->route('super_admin.dashboard');
            } elseif (Auth::check() && (Auth::user()->user_type == 'Admin')) {
                return redirect()->route('admin.dashboard');
            } elseif (Auth::check() && (Auth::user()->user_type == 'Salesman')) {
                return redirect()->route('salesman.dashboard');
            } else {
                return redirect()->route('user.login')->withErrors(['msg' => 'Your Credentials Invalid!']);
            }
        } else {
            return redirect()->route('user.login')->withErrors(['msg' => 'Your Credentials Invalid!']);
        }
    }


    public function logout(Request $request){
        $userId = Auth::id();
        $ip = $request->ip();
        // UserLogingInfo::whereuser_id($userId)->whereip_address($ip)->update(array('log_out' => now()));

        // Operation Log Success
        Helper::operationLog(NULL, 'Logout','Logout',NULL,NULL,'Success',NULL,NULL);

        auth()->logout();

        $request->session()->invalidate();
                return back();
    }
}
