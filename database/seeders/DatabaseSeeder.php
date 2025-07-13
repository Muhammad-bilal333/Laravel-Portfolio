<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use \App\Models\Faculty;
use \App\Models\Student;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a user manually
        // User::create([
        //     'name' => 'Admin User',
        //     'email' => 'student@example.com',
        //     'password' => Hash::make('123'), //  Hash the password
        //     'role_id' => 4, // Set role_id (change based on your roles)
        // ]);
        // Faculty::create([
        //     'user_id' => 4 ,
        //     'designation' => 'hod',
        //     'department' => "Software", //  Hash the password
        // ]);
       
        // Student::create([
        //     'user_id' => 8 ,
        //     'roll_number' => '01',
        //     'Batch' => "FA-19", //  Hash the password
        //     'department' => "Software", //  Hash the password
        // ]);

        // You can use factories as well
        // User::factory(10)->create();
    }
}

