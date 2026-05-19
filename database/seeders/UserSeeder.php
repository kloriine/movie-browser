<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'username' => 'tes',
            'password' => Hash::make('tes'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
