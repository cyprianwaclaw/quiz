<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
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
            $user->address()->save(Address::factory()->make());
        }
        $companies = Company::all();
        foreach ($companies as $company) {
            $company->address()->save(Address::factory()->make());
        }
    }
}
