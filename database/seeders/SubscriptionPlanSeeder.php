<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Plan::create([
            'name' => 'Premium',
            'description' => 'Premium Permanent',
            'price' => 0,
            'is_active' => 0,
            'signup_fee' => 0,
            'invoice_period' => 99,
            'invoice_interval' => 'year',
            'sort_order' => 1,
            'currency' => 'PLN',
            'active_subscribers_limit' => 1000,
        ]);
        Plan::create([
            'name' => 'Premium',
            'description' => 'Premium 7 days',
            'price' => 22,
            'signup_fee' => 0,
            'invoice_period' => 1,
            'invoice_interval' => 'week',
            'sort_order' => 2,
            'currency' => 'PLN',
            'active_subscribers_limit' => 2,
        ]);

        Plan::create([
            'name' => 'Premium',
            'description' => 'Premium 1 month',
            'price' => 72,
            'signup_fee' => 0,
            'invoice_period' => 1,
            'invoice_interval' => 'month',
            'sort_order' => 3,
            'currency' => 'PLN',
        ]);
    }
}
