<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Customer;
use App\Models\Guest;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function __construct() {
        $this->middleware("employee:admin,staff");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reservations = Reservation::with("room", "reservable")->orderBy("created_at", "DESC")->get();
        return view('dashboard/reservation/index', ["reservations" => $reservations]);
    }

    /**
     * Return a listing of the resource with certain attributes.
     *
     * @return array
     */
    public function json(Request $request)
    {
        $roomID = $request->roomID;
        $reservations = Room::find($roomID)->reservations;
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rooms = Room::with("reservations")->get();
        $customers = Customer::all();
        return view('dashboard/reservation/create-form', ["rooms" => $rooms, "customers" => $customers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $split = explode("||", $request->customer, 2);
        $isCustomer = $split[0] == 'c' ? 1 : 0;
        $customerID = $split[1];
        if (!$isCustomer) {
            $customerID = Guest::create([
                "username" => $customerID
            ])->id;
        }
        $customerID = (int) $customerID;
        $validator = Validator::make($request->all(), [
            "startDate" => "required|date|after_or_equal:today",
            "endDate" => "required|date|after_or_equal:startDate"
        ]);
        $validator->after(function ($validator) use ($request) {
            $count = Reservation::where("room_id", $request->roomId)
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
        $error = "";
        if ($request->checkIn) {
            $room = Room::find($request->roomId);
            if ($room->isReserved()) {
                $request->checkIn = false;
                $error = "The room is currently reserved by other customer";
            }
        }
        Reservation::create([
            "room_id" => $request->roomId,
            "start_date" => $request->startDate,
            "end_date" => $request->endDate,
            "reservable_type" => $isCustomer ? Customer::class : Guest::class,
            "reservable_id" => $customerID,
            "check_in" => ($request->checkIn ? Carbon::now() : null)
        ]);
        if ($error == "") {
            return redirect()->route('dashboard.reservation.create')->with("message", "New Reservation Created Successfully");
        }
        else {
            return redirect()->route('dashboard.reservation.create')->with("message", "New Reservation Created Successfully")->with("error", $error);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function show(Reservation $reservation)
    {
        return view('dashboard/reservation/view', ["reservation" => $reservation]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function edit(Reservation $reservation)
    {
        $reservation->load("room", "reservable");
        $rooms = Room::with("reservations")->get();
        $customers = Customer::all();
        return view('dashboard/reservation/edit-form', ["rooms" => $rooms, "customers" => $customers, "reservation" => $reservation]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reservation $reservation)
    {
        $split = explode("||", $request->customer, 2);
        $isCustomer = $split[0] == 'c' ? 1 : 0;
        $customerID = $split[1];
        if (!$isCustomer) {
            $customerID = Guest::create([
                "username" => $customerID
            ])->id;
        }
        $customerID = (int) $customerID;
        $validator = Validator::make($request->all(), [
            "startDate" => "required|date",
            "endDate" => "required|date"
        ]);
        $validator->after(function ($validator) use ($request, $reservation) {
            $count = Reservation::where("id", "!=", $reservation->id)->where("room_id", $request->roomId)
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
            if ($request->checkIn) {
                $room = Room::find($request->roomId);
                if ($room->isReserved() && $room->reservedBy() != null && $room->reservedBy()->isNot($reservation)) {
                    $validator->errors()->add("reserved", "The room is currently reserved by other customer");
                }
            }
        });
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $reservation->room_id = $request->roomId;
        $reservation->start_date = $request->startDate;
        $reservation->end_date = $request->endDate;
        $reservation->reservable_type = $isCustomer ? Customer::class : Guest::class;
        $reservation->check_in = $request->checkIn ? Carbon::now() : null;
        $reservation->reservable_id = $customerID;
        // $reservation->save();

        return redirect()->route('dashboard.reservation.edit', ["reservation" => $reservation])->with("message", "The Reservation Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return response()->json(['success' => "The reservation has been removed"]);
    }

    public function createService(Reservation $reservation)
    {
        $services = Service::all();
        return view('dashboard/reservation/room-service', ["reservation" => $reservation, "services" => $services]);
    }

    public function storeService(Request $request, Reservation $reservation)
    {
        // $arr = array_map(null, $request->serviceID, $request->qty);
        $arr = array_combine($request->serviceID, $request->qty);
        // foreach ($arr as $key => $value) {
        //     $arr[$key] = ["quantity" => $value];
        // }
        // dd($reservation->services);
        // $reservation->services()->sync($arr);
        foreach ($arr as $key => $value) {
            if ($reservation->services()->where("service_id", $key)->exists()) {
                $reservation->services()->where("service_id", $key)->increment("quantity", $value);
            }
            else {
                $reservation->services()->attach($key, [
                    "quantity" => $value
                ]);
            }
        }
        $services = Service::all();
        return redirect()->route('dashboard.reservation.service', ["reservation" => $reservation, "services" => $services])->with("message", "The Room Services Added Successfully");
    }

    public function checkIn(Reservation $reservation)
    {
        if (!$reservation->room->isReserved()) {
            $reservation->check_in = Carbon::now();
            $reservation->save();
        }
        return redirect()->back();
    }
}
