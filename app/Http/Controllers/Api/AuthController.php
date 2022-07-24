<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ActivateEmail;
use App\Models\User;
use App\Service\GenerateActivationCode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    /**
     * Registration using API
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function register(Request $request)
    {

        // Get data from POST
        $data = $request->request->all();

        // Validate request
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);

        // Get activation code
        $activationCode = GenerateActivationCode::generate($data['email']);

        // Create new user
        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'active' => 0,
            'activation_code' => $activationCode,
            'settings' => json_encode(User::$defaultSettings),
        ]);

        // Create token
        // $token = $user->createToken('API', ['scope:all'])->plainTextToken;

        // Send email
        Mail::to($data['email'])->send(new ActivateEmail([
            'title' => __('page.registration.email.title', ['name' => $data['name']]),
            'body' => __('page.registration.email.content', ['url' => Config::get('app.external_url') . '/activate/' . $activationCode])
        ]));

        // Create response
        $response = [
            'message' => 'User has been created, activation email has been sent.',
            // 'user' => $user,
            // 'token' => explode('|', $token)[1],
        ];

        // Return response
        return response($response, 201);

    }

    /**
     * Login using API
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function login(Request $request)
    {

        // Get data from POST
        $data = $request->request->all();

        // Determine login type according to email address validation
        $login_type = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        // Validate request
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string'
        ]);

        // Get user according to username or email
        if($login_type === 'email') {
            $user = User::query()->where('email', $data['login'])->first();
        } else {
            $user = User::query()->where('username', $data['login'])->first();
        }

        // Check if user is active
        if(!$user['active']) {
            return \response([
                'message' => 'User is inactive'
            ], 401);
        }

        // Check if password is valid
        if(!$user || !Hash::check($data['password'], $user->password)) {
            return \response([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Delete old API tokens
        $user->tokens()->delete();

        // Create token
        $token = $user->createToken('API', ['scope:all'])->plainTextToken;

        // Create response
        $response = [
            'user' => $user,
            'token' => explode('|', $token)[1],
        ];

        // Return response
        return response($response, 201);

    }

    /**
     * Logout using API
     *
     * @param Request $request
     * @return string[]
     */
    public function logout(Request $request)
    {

        // Destroy api tokens
        auth()->user()->tokens()->delete();

        // Return logged out message
        return [
            'message' => 'Logged out',
        ];

    }

}
