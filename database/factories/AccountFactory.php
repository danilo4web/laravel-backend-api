<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'balance' => 0,
            'status'=> 1,
            'number'  => $this->faker->unique()->numberBetween(1000009, 9999999),
            'customer_id' => $this->faker->numberBetween(1, Customer::count()),
        ];
    }
}
