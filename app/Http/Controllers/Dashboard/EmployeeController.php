<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\EmployeeCreatedNotification;

class EmployeeController extends Controller
{
    public function __construct() {
        $this->middleware("employee:admin");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('role')) {
            $role = $request->role;
            $employees = Employee::where("role", $role)->where("id", "!=", Auth::guard('employee')->user()->id)->get();
            return view("dashboard/employee/index", ["employees" => $employees, "role" => $role]);
        }
        else {
            $employees = Employee::all()->except(Auth::guard('employee')->user()->id);
            return view("dashboard/employee/index", ["employees" => $employees]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->has('role')) {
            $role = intval($request->role);
            return view("dashboard/employee/create-form", ["role" => $role]);
        }
        return view("dashboard/employee/create-form");
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
            'username' => 'required|max:255|unique:customer,username|unique:employee,username',
            'email' => 'required|email|max:255|unique:customer,email|unique:employee,email',
            'phone' => 'required|regex:/^(\+6)?01[0-46-9]-[0-9]{7,8}$/|max:14',
            'role' => 'required',
        ]);
        $password = Str::random(10);
        $employee = Employee::create([
            "username" => $request->username,
            "email" => $request->email,
            "phone" => $request->phone,
            "role" => $request->role,
            "password" => Hash::make($password)
        ]);
        $employee->notify(new EmployeeCreatedNotification($request->username, $password));
        return redirect()->route('dashboard.employee.create')->with("message", "New Employee Created Successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        return view('dashboard/employee/view', ['employee' => $employee]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        return view("dashboard/employee/edit-form", ["employee" => $employee]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        $this->validate($request, [
            'username' => 'required|max:255|unique:customer,username|unique:employee,username,'.$employee->id,
            'email' => 'required|email|max:255|unique:customer,email|unique:employee,email,'.$employee->id,
            'phone' => 'required|regex:/^(\+6)?01[0-46-9]-[0-9]{7,8}$/|max:14',
            'role' => 'required',
        ]);
        $employee->username = $request->username;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->role = $request->role;
        $employee->save();

        return redirect()->route('dashboard.employee.edit', ['employee' => $employee])->with("message", "The employee has successfully updated");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return string
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->json(['success' => "The employee has been removed"]);
    }
}
