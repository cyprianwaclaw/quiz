<?php

namespace Database\Factories;

use App\Enums\PayoutStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayoutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $points = $this->faker->numberBetween(0, 200);
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'points' => $points,
            'amount' => $points * config('game.points_multiplier'),
            'status' => $this->faker->randomElement(PayoutStatus::TYPES)
        ];
    }
}
