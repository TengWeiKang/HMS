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
        $customer->load(["bookings", "bookings.rooms", "bookings.payment", "bookings.payment.rooms", "bookings.payment.items", "bookings.payment.charges"]);
        // dd($customer->bookings);
        return view("dashboard.customer.view", ["customer" => $customer]);
    }
}
