<?php

namespace App\Http\Controllers\Salesman;
use App\Http\Traits\CurrencyTrait;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('backend.salesman.dashboard');
    }
}
