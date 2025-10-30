<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Jonathan',
                'email' => 'jonathanwong1214@gmail.com',
                'password' => Hash::make('Jwsh1214'),
            ],
            [
                'name' => 'Alice',
                'email' => 'alice@gmail.com',
                'password' => Hash::make('AlicePass123'),
            ],
            [
                'name' => 'Bob',
                'email' => 'bob@gmail.com',
                'password' => Hash::make('BobPass456'),
            ]            
        ]);
    }
}
