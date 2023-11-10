<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $this->faker = \Faker\Factory::create('pl_PL');
        return [
            'city' => $this->faker->city(),
            'postcode' => $this->faker->postcode(),
            'street' => $this->faker->streetName(),
            'building_number' => $this->faker->numerify($this->faker->randomElement(['##A', '%#','##B', '%#'])),
            'house_number' => $this->faker->optional()->numberBetween(1,100),
        ];
    }
}
