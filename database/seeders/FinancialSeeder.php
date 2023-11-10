<?php

namespace Database\Seeders;

use App\Models\Financial;
use App\Models\User;
use Illuminate\Database\Seeder;

class FinancialSeeder extends Seeder
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
            if (random_int(0, 1)) {
                $user->financial()->save(Financial::factory()->make());
            }
        }
    }
}
