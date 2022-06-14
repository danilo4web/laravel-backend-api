<?php

namespace Database\Factories;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Factories\Factory;

class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => mt_rand(0, 1000) . $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$AYLvOEHeXZ5VwkX8Lm93JebW3LJ9yzLpg4bc4k8qhKs8mwxQEhXp2',
            'remember_token' => Str::random(10),
            'is_active' => 1
        ];
    }
}
