<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard/room/index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $facilities = Facility::all();
        return view('dashboard/room/create-form', ["facilities" => $facilities]);
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
                'name' => 'required|max:255',
                'price' => 'required|numeric|min:0.01|regex:/^\d*(\.\d{1,2})?$/',
                'image' => 'required|file|mimes:jpg,png,jpe,jpeg',
                'singleBed' => 'required|min:0|max:20',
                'doubleBed' => 'required|min:0|max:20',
            ],
            [
                'price.regex' => 'The price can only accept 2 decimals'
            ]
        );

        $file = $request->file('image');
        $mimeType = $file->getMimeType();
        $facilities = [];
        if (isset($request->facilities)){
            foreach ($request->facilities as $value) {
                $facilities[] = ["facility_id" => $value];
            }
        }
        $room = Room::create([
            "name" => $request->name,
            "price" => $request->price,
            "room_image" => file_get_contents($request->image),
            "image_type" => $mimeType,
            "single_bed" => $request->singleBed,
            "double_bed" => $request->doubleBed,
        ])->facilities()->attach($facilities);
        return redirect()->route('dashboard.room.create');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function show(Room $room)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function edit(Room $room)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room)
    {
        //
    }
}
