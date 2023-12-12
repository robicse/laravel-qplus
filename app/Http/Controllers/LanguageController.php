<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LanguageController extends Controller
{
    public function index()
    {
        //dd('admin');

        // admin lte
        //return view('backend.admin.dashboard');

        // naisha
        //return view('backend.layouts.app');

        // argoan
        return view('backend.layouts.app');
    }
}
