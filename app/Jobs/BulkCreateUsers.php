<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Hash;

class BulkCreateUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $usersData;

    // Constructor accepts the users' data
    public function __construct($usersData)
    {
        $this->usersData = $usersData;
    }

    public function handle()
    {
        // Insert users in chunks (to optimize memory usage)
        $chunkSize = 100;  // You can adjust this value

        $chunks = array_chunk($this->usersData, $chunkSize);

        foreach ($chunks as $chunk) {
            $users = collect($chunk)->map(function ($userData) {
                return [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']), // Ensure passwords are hashed
                    'role_id' => $userData['role_id'],  // Assuming role_id is passed
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

            // Insert all users in bulk
            User::insert($users->toArray());
        }
    }
}
