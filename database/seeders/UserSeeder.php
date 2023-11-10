<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       /* DB::table('users')
            ->insert([
                [
                    'name' => 'admin',
                    'email' => 'admin@example.com',
                    'email_verified_at' => now(),
                    'password' => Hash::make('admin')
                ],
                [
                    'name' => 'user',
                    'email' => 'user@example.com',
                    'email_verified_at' => now(),
                    'password' => Hash::make('user')
                ]
            ]);
       */
        DB::table('users')
            ->insert([
                [
                    'name' => 'test',
                    'email' => 'test@example.com',
                    'email_verified_at' => now(),
                    'password' => Hash::make('test123456')
                ],
                [
                    'name' => 'test2',
                    'email' => 'test2@example.com',
                    'email_verified_at' => now(),
                    'password' => Hash::make('test123456')
                ],
                [
                    'name' => 'test3',
                    'email' => 'test3@example.com',
                    'email_verified_at' => now(),
                    'password' => Hash::make('test123456')
                ],
            ]);
    }
}
