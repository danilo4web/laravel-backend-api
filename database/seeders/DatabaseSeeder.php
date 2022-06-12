<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(AccountSeeder::class);
        $this->call(CheckSeeder::class);
        $this->call(CheckLogSeeder::class);
        $this->call(TransactionSeeder::class);
    }
}
