<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Check;
use Illuminate\Database\Eloquent\Factories\Factory;

class CheckLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'admin_id' => $this->faker->numberBetween(1, Admin::count()),
            'check_id' => $this->faker->numberBetween(1, Check::count()),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
