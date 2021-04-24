<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('clients')->insert([
            'full_name' => 'Sherlock Holmes',
            'email' => 'example@gmail.com',
            'phone' => '+380000000000',
            'password' => Hash::make('password'),
            'address' => 'london baker street 221b',
            'note' => 'Note about user'
        ]);
    }
}

