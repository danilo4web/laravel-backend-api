<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class CheckFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'file' => $this->faker->imageUrl(),
            'description' => $this->faker->company(),
            'amount' => $this->faker->randomNumber(2, 1, 8),
            'status' => 'pending',
            'account_id' => $this->faker->numberBetween(1, Account::count())
        ];
    }
}
