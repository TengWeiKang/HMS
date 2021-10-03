<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\RoomType;
use Carbon\Carbon;
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
        Auth::user()->load("bookings", "bookings.room", "bookings.room.type", "bookings.services");
        Auth::user()->bookings = Auth::user()->bookings->filter(function ($value){
            return in_array($value->status(), [0, 1]);
        })->sortByDesc("id");
        return view("customer.booking.index");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function history()
    {
        Auth::user()->load("bookings", "bookings.room", "bookings.room.type", "bookings.services", "bookings.payment");
        Auth::user()->bookings = Auth::user()->bookings->filter(function ($value){
            return $value->status() == 2;
        })->sortByDesc("id");
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
    public function create(RoomType $roomType, $singleBed, $doubleBed)
    {
        if (request()->has(["startDate", "endDate"])) {
            $startDate = request()->startDate;
            $endDate = request()->endDate;
            $rooms = $this->roomFilterAvailable($roomType, $singleBed, $doubleBed, $startDate, $endDate);
            return view("customer.booking.create-form", [
                "roomType" => $roomType,
                "singleBed" => $singleBed,
                "doubleBed" => $doubleBed,
                "startDate" => $startDate,
                "endDate" => $endDate,
                "count" => $rooms->count()
            ]);
        }
        return abort(404);
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
    public function store(Request $request, RoomType $roomType, $singleBed, $doubleBed)
    {
        $this->validate($request, [
            "startDate" => "required|date|after_or_equal:today",
            "endDate" => "required|date|after_or_equal:startDate",
            "firstName" => "required|max:255",
            "lastName" => "required|max:255",
            'phone' => 'required|regex:/^(\+6)?01[0-46-9]-[0-9]{7,8}$/|max:14',
        ]);
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $rooms = $this->roomFilterAvailable($roomType, $singleBed, $doubleBed, $startDate, $endDate);
        $room = $rooms[0];
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
        // $validator = Validator::make($request->all(), [
        //     "startDate" => "required|date",
        //     "endDate" => "required|date"
        // ]);
        // $validator->after(function ($validator) use ($request, $booking) {
        //     $count = Reservation::where("id", "!=", $booking->id)->where("room_id", $request->roomId)->where("status", 1)
        //         ->where(function ($query) use ($request) {
        //             $query->where("start_date", "<=", $request->startDate)
        //                 ->where("end_date", ">=", $request->startDate)
        //                 ->orWhere("start_date", "<=", $request->endDate)
        //                 ->where("end_date", ">=", $request->endDate)
        //                 ->orWhere("start_date", ">=", $request->startDate)
        //                 ->where("end_date", "<=", $request->endDate);
        //         }
        //     )->count();
        //     if ($count > 0) {
        //         $validator->errors()->add("dateConflict", "The booking date has conflict with others booking");
        //     }
        // });
        // $validator->validate();

        // $booking->start_date = $request->startDate;
        // $booking->end_date = $request->endDate;
        // $booking->save();

        // return redirect()->route('customer.booking.edit', ["booking" => $booking])->with("message", "The Booking Updated Successfully");
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

    public function roomFilterAvailable($roomType, $singleBed, $doubleBed, $startDate, $endDate) {
        $arrival = new Carbon($startDate);
        $departure = new Carbon($endDate);
        $rooms = Room::with("reservations", "type")
            ->where("room_type", $roomType->id)
            ->where("single_bed", $singleBed)
            ->where("double_bed", $doubleBed)
            ->get();
        $rooms = $rooms->filter(function ($value) use ($arrival, $departure) {
            if (!empty($arrival) && !empty($departure)) {
                $reservations = $value->reservations->filter(function ($value2) use ($arrival, $departure) {
                    if ($value2->start_date->lte($arrival) && $value2->end_date->gte($arrival) ||
                    $value2->start_date->lte($departure) && $value2->end_date->gte($departure) ||
                    $value2->start_date->gte($arrival) && $value2->end_date->lte($departure)) {
                        return true;
                    }
                    return false;
                });
                if ($reservations->count() > 0)
                    return false;
            }
            return true;
        });
        return $rooms;
    }
}
