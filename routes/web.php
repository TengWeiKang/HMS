<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Dashboard\EmployeeController;
use App\Http\Controllers\Dashboard\EmployeeProfileController;
use App\Http\Controllers\Dashboard\EmployeeChangePasswordController;
use App\Http\Controllers\Dashboard\RoomController;
use App\Http\Controllers\Dashboard\FacilityController;
use App\Http\Controllers\Dashboard\ReservationController;
use App\Http\Controllers\Dashboard\ServiceController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ForgetPasswordController;
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
Route::group(["prefix" => 'dashboard', "middleware" => ["employee"]], function () {
    Route::view('/', "dashboard/dashboard")->name("dashboard.home");

    //employee management
    Route::group(["prefix" => "employee"], function() {
        Route::get("/", [EmployeeController::class, "index"])->name("dashboard.employee");
        Route::get("/create", [EmployeeController::class, "create"])->name("dashboard.employee.create");
        Route::post("/create", [EmployeeController::class, "store"]);
        Route::get("/{employee}", [EmployeeController::class, "show"])->name("dashboard.employee.view");
        Route::get("/{employee}/edit", [EmployeeController::class, "edit"])->name("dashboard.employee.edit");
        Route::put("/{employee}/edit", [EmployeeController::class, "update"]);
        Route::delete("/{employee}", [EmployeeController::class, "destroy"])->name("dashboard.employee.destroy");
    });

    //facility management
    Route::group(["prefix" => "facility"], function() {
        Route::get("/", [FacilityController::class, "index"])->name("dashboard.facility");
        Route::get("/create", [FacilityController::class, "create"])->name("dashboard.facility.create");
        Route::post("/create", [FacilityController::class, "store"]);
        // Route::get("/{facility}", [FacilityController::class, "show"])->name("dashboard.facility.view");
        Route::get("/{facility}/edit", [FacilityController::class, "edit"])->name("dashboard.facility.edit");
        Route::put("/{facility}/edit", [FacilityController::class, "update"]);
        Route::delete("/{facility}", [FacilityController::class, "destroy"])->name("dashboard.facility.destroy");
    });

    //profile
    Route::group(["prefix" => "profile"], function() {
        Route::get("/", [EmployeeProfileController::class, "show"])->name("dashboard.profile.view");
        Route::get("/edit", [EmployeeProfileController::class, "edit"])->name("dashboard.profile.edit");
        Route::put("/edit", [EmployeeProfileController::class, "update"]);
        Route::get("/change-password", [EmployeeChangePasswordController::class, "edit"])->name("dashboard.profile.password");
        Route::put("/change-password", [EmployeeChangePasswordController::class, "update"]);
    });

    //room management
    Route::group(["prefix" => "room"], function() {
        Route::get("/", [RoomController::class, "index"])->name("dashboard.room");
        Route::get("/create", [RoomController::class, "create"])->name("dashboard.room.create");
        Route::post("/create", [RoomController::class, "store"]);
        Route::get("/{room}", [RoomController::class, "show"])->name("dashboard.room.view");
        Route::get("/{room}/edit", [RoomController::class, "edit"])->name("dashboard.room.edit");
        Route::put("/{room}/edit", [RoomController::class, "update"]);
        Route::delete("/{room}", [RoomController::class, "destroy"])->name("dashboard.room.destroy");
    });

    //room service management
    Route::group(["prefix" => "service"], function() {
        Route::get("/", [ServiceController::class, "index"])->name("dashboard.service");
        Route::get("/create", [ServiceController::class, "create"])->name("dashboard.service.create");
        Route::post("/create", [ServiceController::class, "store"]);
        // Route::get("/{service}", [ServiceController::class, "show"])->name("dashboard.service.view");
        Route::get("/{service}/edit", [ServiceController::class, "edit"])->name("dashboard.service.edit");
        Route::put("/{service}/edit", [ServiceController::class, "update"]);
        Route::delete("/{service}", [ServiceController::class, "destroy"])->name("dashboard.service.destroy");
    });


    //reservation management
    Route::group(["prefix" => "reservation"], function() {
        Route::get("/", [ReservationController::class, "index"])->name("dashboard.reservation");
        Route::post("/json", [ReservationController::class, "json"])->name("dashboard.reservation.json");
        Route::get("/create", [ReservationController::class, "create"])->name("dashboard.reservation.create");
        Route::post("/create", [ReservationController::class, "store"]);
        Route::get("/{reservation}", [ReservationController::class, "show"])->name("dashboard.reservation.view");
        Route::get("/{reservation}/edit", [ReservationController::class, "edit"])->name("dashboard.reservation.edit");
        Route::put("/{reservation}/edit", [ReservationController::class, "update"]);
        Route::delete("/{reservation}", [ReservationController::class, "destroy"])->name("dashboard.reservation.destroy");
    });
});

Route::get('/register', [RegisterController::class, "index"])->name("register");
Route::post('/register', [RegisterController::class, "store"]);
Route::get('/login', [LoginController::class, "index"])->name("login");
Route::post('/login', [LoginController::class, "store"]);
Route::get('/logout', [LogoutController::class, "index"])->name("logout");

Route::get('/forget-password', [ForgetPasswordController::class, 'index'])->name("password.forget");
Route::post('/forget-password', [ForgetPasswordController::class, 'notifyEmail']);
Route::get('/forget-password/{token}', [ForgetPasswordController::class, 'resetPassword'])->name('password.reset');
Route::post('/forget-password/{token}', [ForgetPasswordController::class, 'changePassword']);
