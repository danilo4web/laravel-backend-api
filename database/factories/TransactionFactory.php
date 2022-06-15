<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Check;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'amount' => $this->faker->randomNumber(2, 1, 8),
            'description' => $this->faker->words(2, true),
            'type' => 'debit',
            'account_id' => $this->faker->numberBetween(1, Account::count()),
            'check_id' => null,
        ];
    }
}
