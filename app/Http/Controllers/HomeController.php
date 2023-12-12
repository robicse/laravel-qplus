<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function about_us(){
        return view("frontend.pages.about_us");
    }

    public function contact_us(){
        return view("frontend.pages.contact_us");
    }

    // public function returnpolicy(){
    //     $page =  Page::where('type', 'return_policy_page')->first();
    //     return view("frontend.policies.returnpolicy", compact('page'));
    // }
}
