<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'full_name' => 'Lennox Lewis',
            'email' => 'admin@gmail.com',
            'phone' => '+380980000000',
            'password' => Hash::make('secret'),
            'note' => 'Admin info'
        ]);
    }
}
