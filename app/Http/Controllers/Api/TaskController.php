<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
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
        $this->middleware('auth:sanctum');
    }

    /**
     * Get tasks
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function tasks()
    {

        // Get user
        $user = Auth::user();

        // Get amount of tasks
        $amount = User::getAmountOfTasks($user);

        // Get all tasks
        $tasks = DB::table('tasks')->where('user_id', '=', $user['id'])->orderBy('sequence')->paginate($amount);

        // Return tasks
        return $tasks;

    }

    /**
     * Get pagination links HTML
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function links()
    {

        // Get user
        $user = Auth::user();

        // Get amount of tasks
        $amount = User::getAmountOfTasks($user);

        // Get all tasks (using authentication)
        $tasks = DB::table('tasks')->where('user_id', '=', $user['id'])->orderBy('sequence')->paginate($amount);

        // Return view
        return view('snippets.pagination', [
            'tasks' => $tasks,
        ]);

    }

    /**
     * Add task
     */
    public function addTask(Request $request)
    {

        // Set default result
        $result = [
            'success' => false,
            'max' => 0,
            'amount' => 0,
        ];

        // Get post data
        $postData = $request->request->all();

        // Check if postData is not empty
        if(!empty($postData) && array_key_exists('name', $postData) && array_key_exists('status', $postData) && array_key_exists('identifier', $postData)) {

            // Get user
            $user = Auth::user();

            // Get amount of tasks
            $amount = User::getAmountOfTasks($user);

            // Create new task
            $task = new Task();

            // Get last sequence
            $lastSequence = Task::query()->where('user_id', $user['id'])->orderBy('sequence', 'DESC')->first();

            // Fill task
            $task->fill([
                'user_id' => $user['id'],
                'name' => $postData['name'],
                'description' => '',
                'status' => $postData['status'],
                'identifier' => $postData['identifier'],
                'sequence' => $lastSequence === null ? 1 : $lastSequence['sequence']+1
            ]);

            // Try catch
            try {

                // Save task
                $task->save();

                // Set success
                $result = [
                    'success' => true,
                    'max' => count(Task::query()->where('user_id', $user['id'])->get()),
                    'amount' => $amount,
                ];

            } catch(\Exception $e) {

                // Get error message
                $errorMessage = $e->getMessage();

            }

        }

        // Return JSON response
        return response(json_encode($result), 200)
            ->header('Content-Type', 'application/json');

    }

    /**
     * Edit task
     */
    public function editTask($identifier, Request $request)
    {

        // Set default success
        $success = false;

        // Get post data
        $postData = $request->request->all();

        // Get user
        $user = Auth::user();

        // Get task
        $task = Task::query()->where('identifier', $identifier)->first();

        // Check if user is allowed to edit this task
        if($task instanceof Task && $task['user_id'] === $user['id']) {

            // Try catch
            try {

                // Update task
                Task::query()->where('id', $task['id'])->update($postData);

                // Notify
                $request->session()->flash('notify', array("icon" => 'fas fa-check', "message" => __('page.homepage.tasks.edit.success'), "type" => 'success'));

                // Set success
                $success = true;

            } catch(\Exception $e) {

                // Get error message
                $errorMessage = $e->getMessage();

            }

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

        // Check if postData is not empty
        if(!empty($postData) && array_key_exists('toggle', $postData)) {

            // Get toggle
            $toggle = $postData['toggle'];

            // Get status
            $status = $toggle === 'true' ? 'new' : 'done';

            // Get user
            $user = Auth::user();

            // Get task by identifier
            $instance = Task::query()->where('identifier', $identifier)->first();

            // Check if instance is instance of Task model
            if($instance instanceof Task && $instance['user_id'] === $user['id']) {

                // Try catch
                try {

                    // Update task
                    Task::query()->where('id', $instance['id'])->update(['status' => $status]);

                    // Set success
                    $success = true;

                } catch(\Exception $e) {

                    // Get error message
                    $errorMessage = $e->getMessage();

                }

            }

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

        // Get user
        $user = Auth::user();

        // Get identifier
        $identifier = $postData['identifier'];

        // Get task by identifier
        $task = Task::query()->where('identifier', $identifier)->first();

        // Check if instance of task model & if user is allowed to delete this task
        if($task instanceof Task && $task['user_id'] === $user['id']) {

            // Try catch
            try {

                // Delete task
                $task->delete();

                // Set success
                $success = true;

            } catch(\Exception $e) {

                // Get error message
                $errorMessage = $e->getMessage();

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

        // Get user
        $user = Auth::user();

        // Try catch
        try {

            // Delete all tasks of user
            Task::query()->where('user_id', $user['id'])->delete();

            // Set success
            $success = true;

        } catch(\Exception $e) {

            // Get error message
            $errorMessage = $e->getMessage();

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

        // Get post data
        $postData = $request->request->all();

        // Get tasks from request
        $tasks = $postData['task'];

        // Set count
        $count = 1;

        // TODO: Use DB transaction to make sure all records are updated -> return success

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

        // Check if query params is not empty
        if(!empty($queryParams)) {

            // Check if empty or not
            if(array_key_exists('empty', $queryParams) && $queryParams['empty'] === "true") {

                // Return view
                return view('snippets.empty');

            } else if(array_key_exists('id', $queryParams) && array_key_exists('status', $queryParams) && array_key_exists('name', $queryParams)) {

                // Return view
                return view('snippets.task', ['id' => $queryParams['id'], 'status' => $queryParams['status'], 'name' => $queryParams['name']]);

            }

        }

        // Return JSON response
        return response('{}', 200)
            ->header('Content-Type', 'application/json');

    }
}
