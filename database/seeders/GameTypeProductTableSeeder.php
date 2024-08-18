<?php

namespace Database\Seeders;

use App\Models\Admin\GameType;
use App\Models\GameTypeProduct;
use Illuminate\Database\Seeder;

class GameTypeProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'product_id' => 1,
                'game_type_id' => 1,
                'image' => 'live33.jpeg',
                'rate' => '1.0000',
            ],
            
            
        ];

        GameTypeProduct::insert($data);
    }
}
