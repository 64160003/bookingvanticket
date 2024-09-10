<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RouteDownSeeder extends Seeder
{
    public function run()
    {
        // Insert data into the route_down table
        DB::table('route_down')->insert([
            [
                'idRouteDown' => 1,
                'Destination' => 'บ้านบึง',
                'Active' => 1,
                'created_at' => '2024-08-23 17:26:52',
                'updated_at' => '2024-08-23 17:26:52',
            ],
            [
                'idRouteDown' => 2,
                'Destination' => 'หนองปรือ',
                'Active' => 1,
                'created_at' => '2024-08-23 17:26:52',
                'updated_at' => '2024-08-23 17:26:52',
            ],
            [
                'idRouteDown' => 3,
                'Destination' => 'สามย่าน',
                'Active' => 1,
                'created_at' => '2024-08-23 17:28:01',
                'updated_at' => '2024-08-23 17:28:01',
            ],
            [
                'idRouteDown' => 4,
                'Destination' => 'จันทบุรี',
                'Active' => 1,
                'created_at' => '2024-08-23 17:28:01',
                'updated_at' => '2024-08-23 17:28:01',
            ],
        ]);
    }
}
