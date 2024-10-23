<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('fields')->insert([
            'fieldname' => 'FIELD UPDATE TEST',
            'description' => 'THIS IS THE DESCRIPTION FOR FIELD UPDATE TEST',
            'picture' => 'IMAGEUPDATE.png',
            'is_archived' => false,
            'user_id' => 7,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
