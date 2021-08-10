<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// customer
Route::group(["prefix" => 'customer'], function () {
    Route::get('/', function () {
        return view('customer/index');
    })->name("customer.home");
});

// admin
Route::group(["prefix" => 'dashboard'], function () {
    Route::get('/', function () {
        return view('dashboard/dashboard');
    });
    Route::get('test', function () {
        return view('dashboard/test');
    });
});

