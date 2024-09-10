<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    public function run()
    {
        // Inserting sample data into the schedule table
        DB::table('schedule')->insert([
            [
                'DepartureTime' => '08:00:00',
                'Active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'DepartureTime' => '10:00:00',
                'Active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'DepartureTime' => '12:00:00',
                'Active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'DepartureTime' => '14:00:00',
                'Active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'DepartureTime' => '18:00:00',
                'Active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more rows as needed
        ]);
    }
}
