<?php

namespace App\Http\Controllers;

use App\helpers;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        // Test...
        // $request->session()->flash('notify', array("icon" => 'fas fa-check', "message" => 'Dikke 10', "type" => 'success'));

        // Return view
        return view('home');

    }
}
