<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware("customer");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Auth::user()->load("bookings", "bookings.room", "bookings.services");
        Auth::user()->bookings = Auth::user()->bookings->filter(function ($value, $key){
            return in_array($value->status(), [0, 1]);
        });
        return view("customer.booking.index");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function history()
    {
        Auth::user()->load("bookings", "bookings.room", "bookings.services", "bookings.payment");
        Auth::user()->bookings = Auth::user()->bookings->filter(function ($value, $key){
            return $value->status() == 2;
        })->sortByDesc("check_out");
        return view("customer.booking.history");
    }

    public function payment(Payment $payment)
    {
        $payment->load("items", "charges", "reservation");
        return view("customer.booking.payment", ["payment" => $payment]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function create(Room $room)
    {
        $room->load("reservations");
        return view("customer.booking.create-form", ["room" => $room]);
    }

    public function json(Request $request)
    {
        $roomID = $request->roomID;
        $reservations = Room::find($roomID)->reservations;
        $reservations = $reservations->filter(function ($value, $key) {
            return $value->status == 1;
        });
        if ($request->has("ignoreID")) {
            $ignoreID = $request->ignoreID;
            $reservations = $reservations->filter(function ($value, $key) use ($ignoreID) {
                return $value->id != $ignoreID;
            });
        }
        $json = [];
        foreach ($reservations as $reservation) {
            $json[] = [
                "start" => $reservation->start_date->format("Y-m-d"),
                "end" => $reservation->end_date->addDays(1)->format("Y-m-d"),
                "rendering" => "background",
                "className" => ["bg-red"],
                "checkin" => $reservation->check_in,
                "checkout" => $reservation->check_out,
            ];
        }
        return $json;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Room $room)
    {
        $validator = Validator::make($request->all(), [
            "startDate" => "required|date|after_or_equal:today",
            "endDate" => "required|date|after_or_equal:startDate",
            "firstName" => "required|max:255",
            "lastName" => "required|max:255",
            'phone' => 'required|regex:/^(\+6)?01[0-46-9]-[0-9]{7,8}$/|max:14',
        ]);
        $validator->validate();
        $validator->after(function ($validator) use ($request, $room) {
            $count = Reservation::where("room_id", $room->id)
            ->where(function ($query) use ($request) {
                $query->where("start_date", "<=", $request->startDate)
                    ->where("end_date", ">=", $request->startDate)
                    ->orWhere("start_date", "<=", $request->endDate)
                    ->where("end_date", ">=", $request->endDate)
                    ->orWhere("start_date", ">=", $request->startDate)
                    ->where("end_date", "<=", $request->endDate);
                }
            )->count();
            if ($count > 0) {
                $validator->errors()->add("dateConflict", "The booking date has conflict with others booking");
            }
        });
        $validator->validate();
        $user = Auth::user();
        $user->first_name = $request->firstName;
        $user->last_name = $request->lastName;
        $user->phone = $request->phone;
        $user->save();

        $booking = Reservation::create([
            "room_id" => $room->id,
            "deposit" => $request->deposit,
            "start_date" => $request->startDate,
            "end_date" => $request->endDate,
            "customer_id" => $user->id,
        ]);
        return redirect()->route('customer.booking.view', ["booking" => $booking]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reservation  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(Reservation $booking)
    {
        $booking->load("room", "services", "payment");
        return view("customer.booking.view", ["booking" => $booking]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Reservation  $booking
     * @return \Illuminate\Http\Response
     */
    public function edit(Reservation $booking)
    {
        $booking->load("room", "room.type", "room.type.facilities");
        return view("customer.booking.edit-form", ["booking" => $booking]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reservation  $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reservation $booking)
    {
        $validator = Validator::make($request->all(), [
            "startDate" => "required|date",
            "endDate" => "required|date"
        ]);
        $validator->after(function ($validator) use ($request, $booking) {
            $count = Reservation::where("id", "!=", $booking->id)->where("room_id", $request->roomId)
                ->where(function ($query) use ($request) {
                    $query->where("start_date", "<=", $request->startDate)
                        ->where("end_date", ">=", $request->startDate)
                        ->orWhere("start_date", "<=", $request->endDate)
                        ->where("end_date", ">=", $request->endDate)
                        ->orWhere("start_date", ">=", $request->startDate)
                        ->where("end_date", "<=", $request->endDate);
                }
            )->count();
            if ($count > 0) {
                $validator->errors()->add("dateConflict", "The booking date has conflict with others booking");
            }
        });
        $validator->validate();

        $booking->start_date = $request->startDate;
        $booking->end_date = $request->endDate;
        $booking->save();

        return redirect()->route('customer.booking.edit', ["booking" => $booking])->with("message", "The Booking Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reservation  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reservation $booking)
    {
        $booking->status = 0;
        $booking->save();
        return response()->json(['success' => "The booking has been cancelled"]);
    }
}
