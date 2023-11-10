<?php

namespace Database\Seeders;

use App\Models\Invite;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InviteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        foreach ($users as $user) {
            $user->invite()->create([
                'token' => Str::random(20),
            ]);
        }
    }
}
