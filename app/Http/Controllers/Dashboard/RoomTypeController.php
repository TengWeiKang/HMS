<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roomTypes = RoomType::with("rooms", "facilities")->get();
        return view("dashboard.roomtype.index", ["roomTypes" => $roomTypes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $facilities = Facility::all();
        return view("dashboard.roomtype.create-form", ["facilities" => $facilities]);
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
            "name" => "required|max:255|unique:room_type,name",
            "singleBed" => "required|numeric|min:0|max:20",
            "doubleBed" => "required|numeric|min:0|max:20",
            'price' => 'required|numeric|min:0.01|regex:/^\d*(\.\d{1,2})?$/',
            'image' => 'required|file|mimes:jpg,png,jpe,jpeg',
        ],
        [
            'price.regex' => 'The price can only accept 2 decimals'
        ]);
        $file = $request->file('image');
        $mimeType = $file->getMimeType();
        $roomType = RoomType::create([
            "name" => $request->name,
            "single_bed" => $request->singleBed,
            "double_bed" => $request->doubleBed,
            "room_image" => file_get_contents($request->image),
            "image_type" => $mimeType,
            "price" => $request->price,
        ]);
        $roomType->facilities()->sync($request->facilities);

        return redirect()->route('dashboard.room-type.create')->with("message", "The new room type has created successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RoomType  $roomType
     * @return \Illuminate\Http\Response
     */
    public function show(RoomType $roomType)
    {
        $roomType->load("facilities", "rooms", "rooms.reservations");
        return view('dashboard.roomtype.view', ["roomType" => $roomType]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RoomType  $roomType
     * @return \Illuminate\Http\Response
     */
    public function edit(RoomType $roomType)
    {
        $roomType->load("facilities");
        $facilities = Facility::all();
        return view("dashboard.roomtype.edit-form", ["roomType" => $roomType, "facilities" => $facilities]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RoomType  $roomType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RoomType $roomType)
    {
        $this->validate($request, [
            "name" => "required|max:255|unique:room_type,name," . $roomType->id,
            "singleBed" => "required|numeric|min:0|max:20",
            "doubleBed" => "required|numeric|min:0|max:20",
            'price' => 'required|numeric|min:0.01|regex:/^\d*(\.\d{1,2})?$/',
            'image' => 'file|mimes:jpg,png,jpe,jpeg',
        ],
        [
            'price.regex' => 'The price can only accept 2 decimals'
        ]);
        if ($request->hasFile("image")) {
            $file = $request->file('image');
            $mimeType = $file->getMimeType();
            $roomType->room_image = file_get_contents($request->image);
            $roomType->image_type = $mimeType;
        }
        $roomType->name = $request->name;
        $roomType->price = $request->price;
        $roomType->single_bed = $request->singleBed;
        $roomType->double_bed = $request->doubleBed;
        $roomType->facilities()->sync($request->facilities);
        $roomType->save();
        return redirect()->route('dashboard.room-type.edit', ["roomType" => $roomType])->with("message", "The room type has updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RoomType  $roomType
     * @return \Illuminate\Http\Response
     */
    public function destroy(RoomType $roomType)
    {
        $roomType->delete();
        $roomType->facilities()->detach();
        return response()->json(['success' => "The room type has been removed"]);
    }
}
