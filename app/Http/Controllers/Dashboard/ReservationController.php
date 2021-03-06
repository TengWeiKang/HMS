<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Customer;
use App\Models\Service;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reservations = Reservation::with("rooms", "customer", "rooms.reservations")->orderBy("created_at", "DESC")->get();
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
        $reservations = $reservations->filter(function ($value) {
            return $value->status == 1;
        });
        if ($request->has("ignoreID")) {
            $ignoreID = $request->ignoreID;
            $reservations = $reservations->filter(function ($value) use ($ignoreID) {
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
     * Return a listing of the resource with certain attributes.
     *
     * @return array
     */
    public function roomSearch(Request $request)
    {
        $rawRoomType = RoomType::with("rooms", "rooms.reservations", "rooms.type.facilities");
        if (!empty($request->roomType)) {
            $rawRoomType = $rawRoomType->where("id", $request->roomType);
        }
        $roomTypes = $rawRoomType->get();
        if (!empty($request->startDate) && !empty($request->endDate)){
            $arrival = new Carbon($request->startDate);
            $departure = new Carbon($request->endDate);
        }
        else {
            return ["results" => []];
        }
        $roomTypes->each(function ($roomType) use ($request, $arrival, $departure) {
            $roomType->rooms = $roomType->rooms->filter(function ($room) use ($request, $arrival, $departure) {
                if ($request->has("roomIgnoreID") && in_array($room->id, $request->roomIgnoreID)) {
                    return false;
                }
                $reservations = $room->reservations->filter(function ($reservation) use ($request, $arrival, $departure) {
                    if ($reservation->status == 0)
                        return false;
                    // ignore reservation id
                    if ($request->has("ignoreID") && $request->ignoreID == $reservation->id)
                        return false;
                    // check reservation with conflicts
                    if ($reservation->start_date->lte($arrival) && $reservation->end_date->gte($arrival) ||
                    $reservation->start_date->lte($departure) && $reservation->end_date->gte($departure) ||
                    $reservation->start_date->gte($arrival) && $reservation->end_date->lte($departure)) {
                        return true;
                    }
                    return false;
                });
                // if there is more than one reservation conflict
                if ($reservations->count() > 0) {
                    return false;
                }
                if (!empty($request->single) && $room->single_bed != $request->single) {
                    return false;
                }
                if (!empty($request->double) && $room->double_bed != $request->double) {
                    return false;
                }
                if (!empty($request->person) && $request->person != $room->single_bed + $room->double_bed * 2) {
                    return false;
                }
                if ($request->checkIn == "true") {
                    if ($room->reservedBy() != null && $request->has("ignoreID") && $room->reservedBy()->id == $request->ignoreID) {
                        return true;
                    }
                    if (!in_array($room->status(), [0, 1])) {
                        return false;
                    }
                }
                return true;
            });
        });
        return $this->convertToJson($roomTypes);
    }

    private function convertToJson($roomTypes) {
        $json["results"] = [];
        foreach ($roomTypes as $roomType) {
            if ($roomType->rooms->count() == 0)
                continue;
            $data["text"] = $roomType->name . " (RM " . number_format($roomType->price, 2) . ")";
            $data["children"] = [];
            foreach ($roomType->rooms as $room) {
                array_push($data["children"], [
                    "id" => $room->id,
                    "text" => $room->room_id . " - " . $room->name . " (" . $room->statusName(false) . ")",
                    "room_id" => $room->room_id,
                    "room_type" => $roomType->name,
                    "room_name" => $room->name,
                    "price" => $roomType->price,
                    "single_bed" => $room->single_bed,
                    "double_bed" => $room->double_bed,
                    "facilities" => $room->type->facilities->pluck("name")->toArray(),
                    "room_available" => $room->status() == 0,
                ]);
            }
            array_push($json["results"], $data);
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
        $roomTypes = RoomType::with("rooms", "rooms.reservations")->get();
        $customers = Customer::all();
        if (request()->has("room_id"))
            $room = Room::with("type")->find(request()->room_id);
        return view('dashboard/reservation/create-form', ["roomTypes" => $roomTypes, "customers" => $customers, "room" => $room ?? null]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "passport" => "required",
            "firstName" => "required",
            "lastName" => "required",
            "email" => "required|email",
            "phone" => "required|regex:/^(\+6)?01[0-46-9]-[0-9]{7,8}$/|max:14",
            "startDate" => "required|date|after_or_equal:today",
            "endDate" => "required|date|after_or_equal:startDate",
        ]);
        $validator->after(function ($validator) use ($request) {
            $conflicts = Reservation::with(["rooms" => function ($query) use ($request) {
                $query->whereIn("room.id", $request->room);
            }])->where("status", 1)
                ->where(function ($query) use ($request) {
                    $query->where("start_date", "<=", $request->startDate)
                        ->where("end_date", ">=", $request->startDate)
                        ->orWhere("start_date", "<=", $request->endDate)
                        ->where("end_date", ">=", $request->endDate)
                        ->orWhere("start_date", ">=", $request->startDate)
                        ->where("end_date", "<=", $request->endDate);
                }
            )->get();
            $count = $conflicts->pluck("rooms")->flatten()->count();

            if ($count > 0) {
                $validator->errors()->add("dateConflict", "The booking date has conflict with others booking");
            }
        });
        $validator->validate();

        $split = explode("||", $request->passport, 2);
        $isCustomer = $split[0] == 'c' ? 1 : 0;
        $customerID = $split[1];
        if (!$isCustomer) {
            $customerID = Customer::create([
                "passport" => $customerID,
                "first_name" => $request->firstName,
                "last_name" => $request->lastName,
                "email" => $request->email,
                "phone" => $request->phone
            ])->id;
        }
        else {
            $customerID = (int) $customerID;
            $customer = Customer::find($customerID);
            $customer->phone = $request->phone;
            $customer->first_name = $request->firstName;
            $customer->last_name = $request->lastName;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->save();
        }
        $error = "";

        $reservation = Reservation::create([
            "deposit" => $request->deposit,
            "start_date" => $request->startDate,
            "end_date" => $request->endDate,
            "customer_id" => $customerID,
            "check_in" => ($request->checkIn ? Carbon::now() : null)
        ]);
        $reservation->rooms()->attach($request->room);
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
        $reservation->load("rooms", "customer", "services", "payment", "rooms.reservations", "rooms.type");
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
        $reservation->load("rooms", "customer", "rooms.type", "rooms.reservations");
        $roomTypes = RoomType::with("rooms")->get();
        $customers = Customer::all();
        return view('dashboard/reservation/edit-form', ["roomTypes" => $roomTypes, "customers" => $customers, "reservation" => $reservation]);
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
        $validator = Validator::make($request->all(), [
            "passport" => "required",
            "firstName" => "required",
            "lastName" => "required",
            "email" => "required|email",
            "phone" => "required|regex:/^(\+6)?01[0-46-9]-[0-9]{7,8}$/|max:14",
            "startDate" => "required|date",
            "endDate" => "required|date|after_or_equal:startDate"
        ]);
        $validator->after(function ($validator) use ($request, $reservation) {
            $conflicts = Reservation::with(["rooms" => function ($query) use ($request) {
                $query->whereIn("room.id", $request->room);
            }])->where("id", "!=", $reservation->id)->where("status", 1)
                ->where(function ($query) use ($request) {
                    $query->where("start_date", "<=", $request->startDate)
                        ->where("end_date", ">=", $request->startDate)
                        ->orWhere("start_date", "<=", $request->endDate)
                        ->where("end_date", ">=", $request->endDate)
                        ->orWhere("start_date", ">=", $request->startDate)
                        ->where("end_date", "<=", $request->endDate);
                }
            )->get();
            $count = $conflicts->pluck("rooms")->flatten()->count();

            if ($count > 0) {
                $validator->errors()->add("dateConflict", "The booking date has conflict with others booking");
            }
        });
        $validator->validate();

        $split = explode("||", $request->passport, 2);
        $isCustomer = $split[0] == 'c' ? 1 : 0;
        $customerID = $split[1];

        if (!$isCustomer) {
            $customerID = Customer::create([
                "passport" => $request->passport,
                "first_name" => $request->firstName,
                "last_name" => $request->lastName,
                "email" => $request->email,
                "phone" => $request->phone
            ])->id;
        }
        else {
            $customerID = (int) $customerID;
            $customer = Customer::find($customerID);
            $customer->phone = $request->phone;
            $customer->first_name = $request->firstName;
            $customer->last_name = $request->lastName;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->save();
        }
        $reservation->start_date = $request->startDate;
        $reservation->end_date = $request->endDate;
        if ($reservation->check_in == null) {
            $reservation->check_in = $request->checkIn ? Carbon::now() : null;
        }
        else if (!$request->checkIn) {
            $reservation->check_in = null; // disable check in
        }
        $reservation->customer_id = $customerID;
        $reservation->save();
        $reservation->rooms()->sync($request->room);

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
        $reservation->rooms()->detach();
        $reservation->services()->detach();
        $reservation->delete();
        return response()->json(['success' => "The reservation has been removed"]);
    }

    public function createService(Reservation $reservation)
    {
        $services = Service::all();
        $reservation->load(["services" => function ($query) {
            $query->orderBy("created_at", "DESC");
        }]);
        return view('dashboard/reservation/room-service', ["reservation" => $reservation, "services" => $services]);
    }

    public function storeService(Request $request, Reservation $reservation)
    {
        $arr = array_combine($request->serviceID, $request->qty);
        $arr = array_map(function ($value) {
            return ["quantity" => $value[0]];
        }, $arr);

        $reservation->services()->attach($arr);
        $services = Service::all();
        return redirect()->route('dashboard.reservation.service', ["reservation" => $reservation])->with("message", "The Room Services Added Successfully");
    }

    public function updateService(Request $request, Reservation $reservation) {
        $arr = array_combine($request->serviceID, $request->quantity);
        foreach ($arr as $serviceID => $quantity) {
            $reservation->services()->updateExistingPivot($serviceID, ["quantity" => $quantity]);
        }
        return redirect()->route('dashboard.reservation.service', ["reservation" => $reservation])->with("message", "The Room Services Updated Successfully");
    }

    public function destroyService(Request $request, Reservation $reservation) {
        $reservation->services()->newPivotStatement()->where("id", $request->serviceID)->delete();
        return response()->json(['success' => "The room service has been remove from the reservation"]);
    }

    public function checkIn(Reservation $reservation)
    {
        if ($reservation->canCheckIn()) {
            $reservation->check_in = Carbon::now();
            $reservation->save();
        }
        return redirect()->back();
    }

    public function cancelled(Reservation $reservation)
    {
        $reservation->status = 0;
        $reservation->save();
        return response()->json(['success' => "The reservation has been updated to cancel"]);
    }
}
