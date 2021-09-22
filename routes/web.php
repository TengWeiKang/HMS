<?php

use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\ChangePasswordController;
use App\Http\Controllers\Customer\RoomController as CustomerRoomController;
use App\Http\Controllers\Dashboard\AnalysisController;
use App\Http\Controllers\Dashboard\CustomerController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\EmployeeController;
use App\Http\Controllers\Dashboard\EmployeeProfileController;
use App\Http\Controllers\Dashboard\EmployeeChangePasswordController;
use App\Http\Controllers\Dashboard\RoomController;
use App\Http\Controllers\Dashboard\FacilityController;
use App\Http\Controllers\Dashboard\ReservationController;
use App\Http\Controllers\Dashboard\ServiceController;
use App\Http\Controllers\Dashboard\PaymentController;
use App\Http\Controllers\Dashboard\HousekeeperController;
use App\Http\Controllers\Dashboard\RoomTypeController;
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
    Route::get('/search', [HomeController::class, "search"])->name("customer.search");

    //profile
    Route::group(["prefix" => "profile"], function() {
        Route::get("/", [ProfileController::class, "show"])->name("customer.profile.view");
        Route::get("/edit", [ProfileController::class, "edit"])->name("customer.profile.edit");
        Route::put("/edit", [ProfileController::class, "update"]);
        Route::get("/change-password", [ChangePasswordController::class, "edit"])->name("customer.profile.password");
        Route::put("/change-password", [ChangePasswordController::class, "update"]);
    });

    //rooms
    Route::group(["prefix" => "room"], function() {
        Route::get("/{room}", [CustomerRoomController::class, "show"])->name("customer.room.view");
    });

    //booking
    Route::group(["prefix" => "booking"], function() {
        Route::get("/", [BookingController::class, "index"])->name("customer.booking");
        Route::post("/json", [BookingController::class, "json"])->name("customer.booking.json");
        Route::get("/history", [BookingController::class, "history"])->name("customer.booking.history");
        Route::get("/{room}/create", [BookingController::class, "create"])->name("customer.booking.create");
        Route::post("/{room}/create", [BookingController::class, "store"]);
        Route::get("/{booking}", [BookingController::class, "show"])->name("customer.booking.view");
        Route::get("/{booking}/edit", [BookingController::class, "edit"])->name("customer.booking.edit");
        Route::put("/{booking}/edit", [BookingController::class, "update"]);
        Route::get("/{payment}/payment", [BookingController::class, "payment"])->name("customer.booking.payment");
        Route::delete("/{booking}", [BookingController::class, "destroy"])->name("customer.booking.destroy");
    });
});

// admin
Route::group(["prefix" => 'dashboard', "middleware" => ["employee"]], function () {
    Route::get('/', [DashboardController::class, "index"])->name("dashboard.home");
    Route::post('/json', [DashboardController::class, "json"])->name("dashboard.json");
    Route::post('/update-reservation', [DashboardController::class, "reservation_date_update"])->name("dashboard.reservation-update");

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
        Route::post("/assign", [RoomController::class, "assign"])->name("dashboard.room.assign");
        Route::post("/self-assign", [RoomController::class, "selfAssign"])->name("dashboard.room.self-assign");
        Route::post("/status", [RoomController::class, "updateStatus"])->name("dashboard.room.status");
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
        Route::get("/{service}/edit", [ServiceController::class, "edit"])->name("dashboard.service.edit");
        Route::put("/{service}/edit", [ServiceController::class, "update"]);
        Route::delete("/{service}", [ServiceController::class, "destroy"])->name("dashboard.service.destroy");
    });

    //reservation management
    Route::group(["prefix" => "reservation"], function() {
        Route::get("/", [ReservationController::class, "index"])->name("dashboard.reservation");
        Route::post("/json", [ReservationController::class, "json"])->name("dashboard.reservation.json");
        Route::post("/search", [ReservationController::class, "roomSearch"])->name("dashboard.reservation.search");
        Route::get("/create", [ReservationController::class, "create"])->name("dashboard.reservation.create");
        Route::post("/create", [ReservationController::class, "store"]);
        Route::get("/{reservation}", [ReservationController::class, "show"])->name("dashboard.reservation.view");
        Route::get("/{reservation}/check-in", [ReservationController::class, "checkIn"])->name("dashboard.reservation.check-in");
        Route::get("/{reservation}/edit", [ReservationController::class, "edit"])->name("dashboard.reservation.edit");
        Route::put("/{reservation}/edit", [ReservationController::class, "update"]);
        Route::put("/{reservation}/cancel", [ReservationController::class, "cancelled"])->name("dashboard.reservation.cancel");
        Route::delete("/{reservation}", [ReservationController::class, "destroy"])->name("dashboard.reservation.destroy");
        Route::get("/{reservation}/service", [ReservationController::class, "createService"])->name("dashboard.reservation.service");
        Route::post("/{reservation}/service", [ReservationController::class, "storeService"]);
    });

    //payment management
    Route::group(["prefix" => "payment"], function() {
        Route::get("/", [PaymentController::class, "index"])->name("dashboard.payment");
        Route::get("{reservation}/create", [PaymentController::class, "create"])->name("dashboard.payment.create");
        Route::post("{reservation}/create", [PaymentController::class, "store"]);
        Route::get("/{payment}", [PaymentController::class, "show"])->name("dashboard.payment.view");
        Route::delete("/{payment}", [PaymentController::class, "destroy"])->name("dashboard.payment.destroy");
    });

    //housekeeper management on current day
    Route::group(["prefix" => "housekeeper"], function() {
        Route::get("/", [HousekeeperController::class, "index"])->name("dashboard.housekeeper");
    });

    //room type management
    Route::group(["prefix" => "room-type"], function() {
        Route::get("/", [RoomTypeController::class, "index"])->name("dashboard.room-type");
        Route::get("/create", [RoomTypeController::class, "create"])->name("dashboard.room-type.create");
        Route::post("/create", [RoomTypeController::class, "store"]);
        Route::get("/{roomType}", [RoomTypeController::class, "show"])->name("dashboard.room-type.view");
        Route::get("/{roomType}/edit", [RoomTypeController::class, "edit"])->name("dashboard.room-type.edit");
        Route::put("/{roomType}/edit", [RoomTypeController::class, "update"]);
        Route::delete("/{roomType}", [RoomTypeController::class, "destroy"])->name("dashboard.room-type.destroy");
    });

    //customer management
    Route::group(["prefix" => "customer"], function() {
        Route::get("/", [CustomerController::class, "index"])->name("dashboard.customer");
        Route::get("/{customer}", [CustomerController::class, "show"])->name("dashboard.customer.view");
    });

    //data analysis controller
    Route::group(["prefix" => "analysis"], function() {
        Route::get("/", [AnalysisController::class, "index"])->name("dashboard.analysis");
        Route::get("/json", [AnalysisController::class, "json"])->name("dashboard.analysis.json");
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
