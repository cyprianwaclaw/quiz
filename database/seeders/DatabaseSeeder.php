<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            UserSeeder::class,

            RoleSeeder::class,
            PermissionSeeder::class,
            AssignPermissionSeeder::class,
            AssignRoleSeeder::class,

            UserStatsSeeder::class,
            CompanySeeder::class,
            FinancialSeeder::class,
            AddressSeeder::class,
            InviteSeeder::class,
            SubscriptionPlanSeeder::class,
            CategorySeeder::class,
            QuizSeeder::class,
            QuestionSeeder::class,

            PayoutSeeder::class,

        ]);
    }
}
