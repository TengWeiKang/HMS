<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\AssignHousekeeperMail;
use App\Models\Room;
use App\Models\Facility;
use App\Models\Employee;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RoomController extends Controller
{
    public function __construct() {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rooms = Room::with("type", "housekeeper", "reservations")->get();
        $housekeepers = Employee::where("role", 2)->get();
        return view('dashboard/room/index', ["rooms" => $rooms, "housekeepers" => $housekeepers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $facilities = Facility::all();
        $roomTypes = RoomType::all();
        return view('dashboard/room/create-form', ["facilities" => $facilities, "roomTypes" => $roomTypes]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'roomId' => 'required|max:255|unique:room,room_id',
            'name' => 'required|max:255',
            'image' => 'file|mimes:jpg,png,jpe,jpeg',
            'singleBed' => 'required|numeric|min:0|max:20',
            'doubleBed' => 'required|numeric|min:0|max:20',
        ]);
        $file = $request->file('image');
        $mimeType = optional($file)->getMimeType();
        Room::create([
            "room_id" => $request->roomId,
            "name" => $request->name,
            "price" => $request->price,
            "room_type" => $request->roomType,
            "room_image" => $file ? file_get_contents($file) : null,
            "image_type" => $mimeType,
            "single_bed" => $request->singleBed,
            "double_bed" => $request->doubleBed,
        ])->facilities()->attach($request->facilities);
        return redirect()->route('dashboard.room.create')->with("message", "The room has created successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function show(Room $room)
    {
        $room->load(["reservations" => function ($query) {
            $query->whereNotNull("check_in")->orderBy("start_date", "DESC");
        }, "type", "facilities", "housekeeper", "reservations.payment", "reservations.reservable"]);
        $housekeepers = Employee::where("role", 2)->get();
        return view('dashboard/room/view', ['room' => $room, 'housekeepers' => $housekeepers]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function edit(Room $room)
    {
        $room->load(["type", "facilities"]);
        $facilities = Facility::all();
        $roomTypes = RoomType::all();
        return view('dashboard/room/edit-form', ["room" => $room, "facilities" => $facilities, "roomTypes" => $roomTypes]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Room $room)
    {
        $this->validate($request, [
            'roomId' => 'required|max:255|unique:room,room_id,'.$room->id,
            'name' => 'required|max:255',
            'image' => (!$request->default && !$room->room_image ? "required|" : "") . 'file|mimes:jpg,png,jpe,jpeg',
            'singleBed' => 'required|numeric|min:0|max:20',
            'doubleBed' => 'required|numeric|min:0|max:20',
        ]);
        if ($request->default) {
            $room->room_image = null;
            $room->image_type = null;
        }
        else if ($request->hasFile("image")) {
            $file = optional($request->file('image'));
            $mimeType = optional($file)->getMimeType();
            $room->room_image = file_get_contents($request->image);
            $room->image_type = $mimeType;
        }

        $room->room_id = $request->roomId;
        $room->name = $request->name;
        $room->room_type = $request->roomType;
        $room->single_bed = $request->singleBed;
        $room->double_bed = $request->doubleBed;
        $room->save();
        $room->facilities()->sync($request->facilities);
        return redirect()->route('dashboard.room.edit', ["room" => $room])->with("message", "The room has successfully updated");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room)
    {
        $room->facilities()->detach();
        $room->delete();
        return response()->json(['success' => "The room has been removed"]);
    }

    public function assign(Request $request) {
        $housekeeper = Employee::findOrFail($request->housekeeper);
        $room = Room::findOrFail($request->id);
        Mail::to($housekeeper)->send(new AssignHousekeeperMail($housekeeper, $room));
        $room->status = 2;
        $room->housekeep_by = $request->housekeeper;
        $room->save();
        return redirect()->back();
    }

    public function updateStatus(Request $request) {
        $room = Room::findOrFail($request->id);
        $room->status = $request->status;
        $room->housekeep_by = null;
        $room->note = $request->note;
        $room->save();
        return redirect()->back();
    }
}
