<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\RoomType;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function __construct() {
        $this->middleware("employee:admin,frontdesk");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $facilities = Facility::all();
        return view('dashboard/facility/index', ["facilities" => $facilities]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roomTypes = RoomType::all();
        return view('dashboard/facility/create-form', ["roomTypes" => $roomTypes]);
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
            'facility' => 'required|max:255|unique:facility,name',
        ]);

        $facility = Facility::create([
            "name" => $request->facility,
        ]);
        $facility->roomTypes()->sync($request->roomTypes);
        return redirect()->route('dashboard.facility.create')->with("message", "New Facility Created Successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Facility  $facility
     * @return \Illuminate\Http\Response
     */
    public function show(Facility $facility)
    {
        // return view('dashboard/facility/view', ['facility' => $facility]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Facility  $facility
     * @return \Illuminate\Http\Response
     */
    public function edit(Facility $facility)
    {
        $facility->load("roomTypes");
        $roomTypes = RoomType::all();
        return view('dashboard/facility/edit-form', ["facility" => $facility, "roomTypes" => $roomTypes]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Facility  $facility
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Facility $facility)
    {
        $this->validate($request, [
            'facility' => 'required|max:255|unique:facility,name,'.$facility->id
        ]);
        $facility->roomTypes()->sync($request->roomTypes);
        $facility->name = $request->facility;
        $facility->save();
        return redirect()->route('dashboard.facility.edit', ['facility' => $facility])->with("message", "The facility has successfully updated");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Facility $facility)
    {
        $facility->roomTypes()->detach();
        $facility->delete();
        return response()->json(['success' => "The facility has been removed"]);
    }
}
