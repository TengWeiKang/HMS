<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Dashboard\EmployeeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
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
    Route::get('/', [HomeController::class, "index"])->name("customer.home");
});

// admin
Route::group(["prefix" => 'dashboard'], function () {
    Route::get('/', function () {
        return view('dashboard/dashboard');
    })->name("dashboard.home");
    Route::get('test', function () {
        return view('dashboard/test');
    });
    Route::get('test2', function () {
        return view('dashboard/test2');
    });

    //employee management
    Route::group(["prefix" => 'employee'], function() {
        Route::get("/", [EmployeeController::class, "index"])->name("dashboard.employee");
        Route::get("/create-form", [EmployeeController::class, "create"])->name("dashboard.employee.create");
        Route::post("/create-form", [EmployeeController::class, "store"]);
    });
});

Route::get('/register', [RegisterController::class, "index"])->name("register");
Route::post('/register', [RegisterController::class, "store"]);
Route::get('/login', [LoginController::class, "index"])->name("login");
Route::post('/login', [LoginController::class, "store"]);
Route::get('/logout', [LogoutController::class, "index"])->name("logout");
