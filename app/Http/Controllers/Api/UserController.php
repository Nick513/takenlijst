<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Update amount of tasks
     */
    public function updateAmountOfTasks(Request $request)
    {

        // Set default success
        $success = false;

        // Get user
        $user = Auth::user();

        // Get post data
        $postData = $request->request->all();

        // Check if post data has amount key
        if(array_key_exists('amount', $postData)) {

            // Get settings of user
            $s = $user['settings'];
            $a = json_decode($s, true);

            // Set amount
            $a['tasks']['amount'] = intval($postData['amount']);

            // Try catch
            try {

                // Delete all tasks of user
                User::query()->where('id', $user['id'])->update(['settings' => json_encode($a)]);

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

}
