<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing users data
        // DB::table('users')->truncate();

        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('test1234'),
            'role' => 'admin',
        ]);

        // Create regular users
        User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('test1234'),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Editor',
            'email' => 'editor@example.com',
            'password' => Hash::make('test1234'),
            'role' => 'user',
        ]);
    }
}