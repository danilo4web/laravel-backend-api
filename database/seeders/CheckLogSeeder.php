<?php

namespace Database\Seeders;

use App\Models\CheckLog;
use Illuminate\Database\Seeder;

class CheckLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CheckLog::factory()->times(20)->create();
    }
}
