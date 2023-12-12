


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\DashboardController;

Route::group([
  'as' => 'super_admin.',
  'prefix' => 'super_admin',
  'middleware' => ['auth', 'super_admin']
], function () {
  Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
  //common
  include __DIR__ . '/common.php';
});

