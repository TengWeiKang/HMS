<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::with("bookings")->get();
        return view("dashboard.customer.index", ["customers" => $customers]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        $customer->load(["bookings" => function ($query) {
            $query->where("status", 1)->orderBy("created_at", "DESC");
        }, "bookings.rooms", "bookings.payment"]);

        return view("dashboard.customer.view", ["customer" => $customer]);
    }
}
