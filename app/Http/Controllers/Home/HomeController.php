<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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

            // Get search
            $search = '';

            // Get queryParams
            $queryParams = $request->query->all();

            // Check if search is found
            if(array_key_exists('search', $queryParams)) {

                // Get search
                $search = $queryParams['search'];

            }

            // Get amount of tasks
            $amount = User::getAmountOfTasks($user);

            // Get tasks
            $tasks = Task::query()
                ->where('user_id', '=', $user['id'])
                ->where('name', 'LIKE', '%'.$search.'%')
                ->orderBy('sequence')
                ->paginate($amount);

        }

        // Return view
        return view('home.home', [
            'user' => $user,
            'tasks' => $tasks,
        ]);

    }
}
