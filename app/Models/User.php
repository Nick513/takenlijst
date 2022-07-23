<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Set default settings
     */
    public static $defaultSettings = [
        'tasks' => [
            'amount' => 10,
        ]
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'active',
        'activation_code',
        'settings'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get tasks
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getTasks() {
        return $this->hasMany('App\Models\Task', 'user_id', 'id');
    }

    /**
     * Get amount of tasks related to user settings
     *
     * @param $user
     * @return void
     */
    public static function getAmountOfTasks($user) {

        // Get settings
        $settings = json_decode($user['settings'], true);

        // Check if settings is array
        if(is_array($settings)) {

            // Get amount of tasks
            if (array_key_exists('tasks', $settings) && array_key_exists('amount', $settings['tasks'])) {

                // Set amount
                $amount = json_decode($settings['tasks']['amount'], true);

            } else {

                // Set amount
                $amount = 10;

            }

        } else {

            // Set amount
            $amount = 10;

        }

        // Return amount
        return $amount;

    }
}
