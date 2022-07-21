<?php

namespace App\Http\Controllers\Modal;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ModalController extends Controller
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
     * Load modal
     */
    public function load(Request $request)
    {

        // Get query parameters
        $queryParams = $request->query->all();

        // Check if user is authenticated
        if(Auth::check()) {

            // Check if query params is not empty
            if(!empty($queryParams) && array_key_exists('view', $queryParams)) {

                // Return view
                return view('modals.'.$queryParams['view'], $queryParams['additional']);

            }

        }

        // Return JSON response
        return response('{}', 200)
            ->header('Content-Type', 'application/json');

    }
}
