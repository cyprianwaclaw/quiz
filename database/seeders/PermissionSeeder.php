<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'activate quiz']);
        Permission::create(['name' => 'deactivate quiz']);
        Permission::create(['name' => 'delete quiz']);
    }
}
