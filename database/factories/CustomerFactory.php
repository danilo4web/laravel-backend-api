<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'social_number' => $this->faker->numberBetween(1000000, 999999999),
            'name' => $this->faker->name(),
            'address' => $this->faker->address(),
            'status' => $this->faker->boolean(),
            'user_id' => $this->faker->numberBetween(1, User::count())
        ];
    }
}
