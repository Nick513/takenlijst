<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Get tasks
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function tasks()
    {

        // Set tasks
        $tasks = [];

        // Get user
        $user = Auth::user();

        // Check if user is authenticated
        if(Auth::check()) {

            // Get all tasks (using authentication)
            $tasks = DB::table('tasks')->where('user_id', '=', $user['id'])->orderBy('sequence')->paginate(10);

        } else {

            // Get all tasks (public)
            // $tasks = DB::table('tasks')->paginate(10);

        }

        // Return tasks
        return $tasks;

    }

    /**
     * Add task
     */
    public function addTask(Request $request)
    {

        // Set default success
        $success = false;

        // Get post data
        $postData = $request->request->all();

        // Check if user is authenticated
        if(Auth::check()) {

            // Get user
            $user = Auth::user();

            // Create new task
            $task = new Task();

            // Get last sequence
            $lastSequence = Task::query()->where('user_id', $user['id'])->orderBy('sequence', 'DESC')->first();

            // Fill task
            $task->fill([
                'user_id' => $user['id'],
                'name' => $postData['name'],
                'description' => '...',
                'status' => $postData['status'],
                'identifier' => $postData['identifier'],
                'sequence' => $lastSequence === null ? 1 : $lastSequence['sequence']+1
            ]);

            // Save task
            $task->save();

            // Set success
            $success = true;

        }

        // Return JSON response
        return response(json_encode($success), 200)
            ->header('Content-Type', 'application/json');

    }

    /**
     * Edit task
     */
    public function editTask($identifier, Request $request)
    {

        dd($identifier);

        // Set default success
        $success = false;

        // Get post data
        $postData = $request->request->all();

        // Check if user is authenticated
        if(Auth::check()) {

            // Get user
            $user = Auth::user();

            // Set success
            $success = true;

        }

        // Return JSON response
        return response(json_encode($success), 200)
            ->header('Content-Type', 'application/json');

    }

    /**
     * Toggle task
     */
    public function toggleTask($identifier, Request $request)
    {

        // Set default success
        $success = false;

        // Get post data
        $postData = $request->request->all();

        // Get toggle
        $toggle = $postData['toggle'];

        // Get status
        $status = $toggle === 'true' ? 'new' : 'done';

        // Check if user is authenticated
        if(Auth::check()) {

            // Get task by identifier
            $instance = Task::query()->where('identifier', $identifier)->first();

            // Update task
            Task::query()->where('id', $instance['id'])->update(['status' => $status]);

            // Set success
            $success = true;

        }

        // Return JSON response
        return response(json_encode($success), 200)
            ->header('Content-Type', 'application/json');

    }

    /**
     * Delete task
     */
    public function deleteTask(Request $request)
    {

        // Set default success
        $success = false;

        // Get post data
        $postData = $request->request->all();

        // Check if user is authenticated
        if(Auth::check()) {

            // Get user
            $user = Auth::user();

            // Get identifier
            $identifier = $postData['identifier'];

            // Get task by identifier
            $task = Task::query()->where('identifier', $identifier)->first();

            // Check if instance of task model & if user is allowed to delete this task
            if($task instanceof Task && $task['user_id'] === $user['id']) {

                // Delete task
                $task->delete();

                // Set success
                $success = true;

            }

        }

        // Return JSON response
        return response(json_encode($success), 200)
            ->header('Content-Type', 'application/json');

    }

    /**
     * Delete all tasks
     */
    public function deleteAllTasks(Request $request)
    {

        // Set default success
        $success = false;

        // Check if user is authenticated
        if(Auth::check()) {

            // Get user
            $user = Auth::user();

            // Delete all tasks of user
            Task::query()->where('user_id', $user['id'])->delete();

            // Set success
            $success = true;

        }

        // Return JSON response
        return response(json_encode($success), 200)
            ->header('Content-Type', 'application/json');

    }

    /**
     * Order tasks
     */
    public function orderTasks(Request $request)
    {

        // Set default success
        $success = false;

        // Get post data
        $postData = $request->request->all();

        // Check if user is authenticated
        if(Auth::check()) {

            // Get user
            $user = Auth::user();

            // Get tasks from request
            $tasks = $postData['task'];

            // Set count
            $count = 1;

            // Loop over tasks
            foreach($tasks as $key => $task) {

                // Get task by identifier
                $instance = Task::query()->where('identifier', $task)->first();

                // Update task
                Task::query()->where('id', $instance['id'])->update(['sequence' => $count]);

                // Increment count
                $count++;

            }

            // Set success
            $success = true;

        }

        // Return JSON response
        return response(json_encode($success), 200)
            ->header('Content-Type', 'application/json');

    }

    /**
     * Get snippet
     */
    public function snippet(Request $request)
    {

        // Get query parameters
        $queryParams = $request->query->all();

        // Check if empty or not
        if(array_key_exists('empty', $queryParams) && $queryParams['empty'] === "true") {

            // Return view
            return view('snippets.empty');

        }

        // Return view
        return view('snippets.task', ['id' => $queryParams['id'], 'status' => $queryParams['status'], 'name' => $queryParams['name']]);

    }
}
