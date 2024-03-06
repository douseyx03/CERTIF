<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'firstname' => 'Seydou',
            'lastname' => 'Diallo',
            'email' => 'dousey2003@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_blocked' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'firstname' => 'Abdou',
            'lastname' => 'Diallo',
            'email' => 'abdou@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'is_admin' => false,
            'is_blocked' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'firstname' => 'Dédé',
            'lastname' => 'Diop',
            'email' => 'dede@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'is_admin' => false,
            'is_blocked' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'firstname' => 'Kabir',
            'lastname' => 'Diallo',
            'email' => 'kabir@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'is_admin' => false,
            'is_blocked' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
