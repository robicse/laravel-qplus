<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserLoginController;
use Illuminate\Support\Facades\Artisan;

Route::get('optimize-clear', function () {
    $exitCode = Artisan::call('optimize:clear');
    Toastr::success('Optimize Clear Successfully Done!');
    return "success";
});

Route::get('optimize', function () {
    $exitCode = Artisan::call('optimize');
    Toastr::success('Optimize Successfully Done!');
    return "success";
});

Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    Toastr::success('Cache Clear Successfully Done!');
    return "success";
});
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    Toastr::success('Config Cache Successfully Done!');
    return "success";
});
Route::get('/view-cache', function() {
    $exitCode = Artisan::call('view:cache');
    Toastr::success('View Cache Successfully Done!');
    return "success";
});
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('view:clear');
    Toastr::success('View Clear Successfully Done!');
    return "success";
});

// landing page
Route::get('/', function () {
    return view('auth/login');
});

Auth::routes();
// phone or email login
Route::post('/login', [UserLoginController::class, 'login'])->name('user.login');
Route::post('/logout', [UserLoginController::class, 'logout'])->name('logout');


