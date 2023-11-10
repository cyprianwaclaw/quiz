<?php

namespace Database\Factories;

use Faker\Provider\pl_PL\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialFactory extends Factory
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
            'iban' => $this->faker->bankAccountNumber(),
            'bank_name' => $this->faker->bank(),
            'swift' => $this->faker->swiftBicNumber(),
            'card_number' => $this->faker->creditCardNumber(),
            'card_expiration_date' => $this->faker->creditCardExpirationDate(),
            'card_cvc' => $this->faker->numerify()
        ];
    }
}
