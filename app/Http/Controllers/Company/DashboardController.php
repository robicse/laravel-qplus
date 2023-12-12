<?php

namespace App\Http\Controllers\Company;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        //dd('ss');
        return view('backend.company.dashboard');
    }
}
