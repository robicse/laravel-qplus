<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::group(['as'=>'agent.','prefix' =>'agent', 'middleware' => ['auth', 'agent']], function(){

});

