<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
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
        return view('settings.settings', [
            'user' => $user,
            'session' => $session,
        ]);

    }
}
