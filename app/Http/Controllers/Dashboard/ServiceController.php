<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct() {
        $this->middleware("employee:admin");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Service::all();
        return view('dashboard/service/index', ["services" => $services]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard/service/create-form');
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
            'name' => 'required|max:255|unique:service,name',
            'price' => 'required|numeric|min:0|max:100',
        ]);

        Service::create([
            "name" => $request->name,
            "price" => $request->price,
        ]);
        return redirect()->route('dashboard.service.create')->with("message", "New Room Service Created Successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        return view('dashboard/service/edit-form', ["service" => $service]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        $this->validate($request, [
            'name' => 'required|max:255|unique:service,name,'.$service->id,
            'price' => 'required|numeric|min:0|max:100',
        ]);
        $service->name = $request->name;
        $service->price = $request->price;
        $service->save();
        return redirect()->route('dashboard.service.edit', ['service' => $service])->with("message", "The service has successfully updated");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return response()->json(['success' => "The service has been removed"]);
    }
}
