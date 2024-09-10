<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RouteUpSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear the existing data
        DB::table('route_up')->truncate();

        // Insert new data
        DB::table('route_up')->insert([
            [
                'RouteID' => 1,
                'Origin' => 'คิวรถ',
                'Active' => 1,
                'created_at' => '2024-08-23 17:25:19',
                'updated_at' => '2024-08-23 17:25:19',
            ],
            [
                'RouteID' => 2,
                'Origin' => 'บ้านบึง',
                'Active' => 1,
                'created_at' => '2024-08-23 17:25:32',
                'updated_at' => '2024-08-23 17:25:32',
            ],
            [
                'RouteID' => 3,
                'Origin' => 'หนองปรือ',
                'Active' => 1,
                'created_at' => '2024-08-23 17:25:46',
                'updated_at' => '2024-08-23 17:25:46',
            ],
        ]);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
