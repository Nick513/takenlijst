<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
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
    public function index(Request $request)
    {

        // Set default tasks
        $tasks = [];

        // Set default user
        $user = false;

        // Check if logged in
        if(Auth::check()) {

            // Get user
            $user = Auth::user();

            // Get amount of tasks
            $amount = User::getAmountOfTasks($user);

            // Get tasks
            $tasks = DB::table('tasks')->where('user_id', '=', $user['id'])->orderBy('sequence')->paginate($amount);

        }

        // Return view
        return view('home', [
            'user' => $user,
            'tasks' => $tasks,
        ]);

    }

    /**
     * Settings
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function settings(Request $request)
    {

        // Get user
        $user = Auth::user();

        // Get session
        $session = $request->session();

        // Return view
        return view('settings', [
            'user' => $user,
            'session' => $session,
        ]);

    }
}
