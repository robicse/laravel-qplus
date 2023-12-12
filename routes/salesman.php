<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Salesman\DashboardController;

Route::group([
  'as' => 'salesman.',
  'prefix' => 'salesman',
  'middleware' => ['auth', 'salesman']
], function () {
  Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
  //common
  include __DIR__ . '/common.php';
});
