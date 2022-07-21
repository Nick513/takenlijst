<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
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
    public function index()
    {

        // Create empty tasks
        $tasks = [];

        // Check if logged in
        if(Auth::check()) {

            // Get user
            $user = Auth::user();

            // Get tasks
            $tasks = DB::table('tasks')->where('user_id', '=', $user['id'])->orderBy('sequence')->paginate(10);

        }

        // Return view
        return view('home', [
            'tasks' => $tasks,
        ]);

    }
}
