<?php

namespace Database\Factories;

use Faker\Provider\pl_PL\Company;
use Faker\Provider\pl_PL\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
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
            'name' => $this->faker->company(),
            'nip' => $this->faker->taxpayerIdentificationNumber(),
            'regon' => $this->faker->regon(),
        ];
    }
}
