<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = Carbon::now();
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'guard_name' => 'web',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [   'name' => 'user',
                'guard_name' => 'web',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [   'name' => 'premium',
                'guard_name' => 'web',
                'created_at' => $date,
                'updated_at' => $date
            ]
        ]);
    }
}
