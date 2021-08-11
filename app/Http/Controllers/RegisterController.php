<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisterController extends Controller
{
    function __construct() {
        $this->middleware("guest");
    }
}
