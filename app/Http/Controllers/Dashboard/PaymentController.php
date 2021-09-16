<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payments = Payment::with("items", "charges", "reservation.reservable")->get();
        return view('dashboard/payment/index', ["payments" => $payments]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Reservation $reservation)
    {
        return view('dashboard/payment/create-form', ["reservation" => $reservation]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Reservation $reservation)
    {
        $this->validate($request, [
            'discount' => 'required|numeric|min:0|max:100|regex:/^\d*(\.\d{1,2})?$/',
            'description' => 'array',
            'description.*' => 'required|max:255',
            'chargePrices' => 'array',
            'chargePrices.*' => 'required|numeric|min:0.01|regex:/^\d*(\.\d{1,2})?$/',
        ]);
        $items = [];
        foreach ($reservation->services as $service) {
            $items[] = [
                "service_id" => $service->id,
                "service_name" => $service->name,
                "quantity" => $service->pivot->quantity,
                "unit_price" => $service->price
            ];
        }
        $charges = [];
        if (!is_null($request->description)) {
            for ($i = 0; $i < count($request->description); $i++) {
                $charges[] = [
                    "description" => $request->description[$i],
                    "price" => $request->chargePrices[$i]
                ];
            }
        }
        $payment = Payment::create([
            "reservation_id" => $reservation->id,
            "room_name" => $reservation->room->room_id . " - " . $reservation->room->name,
            "price_per_night" => $reservation->room->price,
            "start_date" => $reservation->start_date,
            "end_date" => $reservation->end_date,
            "discount" => $request->discount
        ]);
        $payment->items()->createMany($items);
        $payment->charges()->createMany($charges);

        $reservation->room->status = 2;
        $reservation->room->housekeeper = null;
        $reservation->room->save();
        $reservation->check_out = Carbon::now();
        $reservation->save();

        return redirect()->route('dashboard.payment.view', ["payment" => $payment]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        $payment->load("items", "charges", "reservation");
        return view('dashboard/payment/view', ["payment" => $payment]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
