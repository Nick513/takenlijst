<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ActivateEmail;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Service\GenerateActivationCode;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Properties
     */
    private $request;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('guest');
        $this->request = $request;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\Models\User
     * @throws AuthenticationException|ValidationException
     */
    protected function create(array $data)
    {

        // Get domain from email
        $p = explode("@", $data['email']);
        $domain = array_pop($p);

        // Check if domain has valid DNS records
        if(!checkdnsrr($domain)) {

            // Throw exception
            throw ValidationException::withMessages(['dns' => __('Please enter a valid email address')]);

        }

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
        ]);

        // Create event
        event(new Registered($user));

        // Send email
        Mail::to($data['email'])->send(new ActivateEmail([
            'title' => __('registration.email.title', ['name' => $data['name']]),
            'body' => __('registration.email.content', ['url' => Config::get('app.external_url') . '/activate/' . $activationCode])
        ]));

        // Notify
        $this->request->session()->flash('notify', array("icon" => 'fas fa-check', "message" => __('registration.email.send'), "type" => 'success'));

        // Throw exception
        throw new AuthenticationException();

    }

    /**
     * Activate account
     */
    public function activate(string $code)
    {

        // Get user by code
        $user = User::query()->where('activation_code', $code)->first();

        // Check if user is found
        if($user instanceof User) {

            // Check if user is already activated or not
            if($user['active'] === 1) {

                // Notify
                $this->request->session()->flash('notify', array("icon" => 'fas fa-times', "message" => __('registration.account.already.activated'), "type" => 'danger'));

            } else {

                // Activate user
                $user->update(['active' => 1]);

                // Notify
                $this->request->session()->flash('notify', array("icon" => 'fas fa-check', "message" => __('registration.account.activated'), "type" => 'success'));

            }

        } else {

            // Notify
            $this->request->session()->flash('notify', array("icon" => 'fas fa-times', "message" => __('registration.account.not.found'), "type" => 'danger'));

        }

        // Return to homepage
        return redirect()->route('home');

    }
}
