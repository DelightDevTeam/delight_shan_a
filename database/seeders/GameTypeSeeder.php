<?php

namespace Database\Seeders;

use App\Models\Admin\GameType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Other',
                'name_mm' => 'အခြား',
                'code' => '0',
                'order' => '1',
                'status' => 0,
                'img' => 'slots.png',
            ],
            [
                'name' => 'Slots',
                'name_mm' => 'စလော့',
                'code' => '1',
                'order' => '2',
                'status' => 1,
                'img' => 'slots.png',
            ],
            [
                'name' => 'Arcade',
                'name_mm' => 'တိုက်ရိုက်ကာစီနို',
                'code' => '2',
                'order' => '3',
                'status' => 1,
                'img' => 'live_casino.png',
            ],
            [
                'name' => 'Table',
                'name_mm' => 'အားကစား',
                'code' => '3',
                'order' => '4',
                'status' => 1,
                'img' => 'sportbook.png',
            ],
            [
                'name' => 'Event',
                'name_mm' => 'အဖြစ်',
                'code' => '4',
                'order' => '5',
                'status' => 1,
                'img' => 'fishing.png',
            ],
            [
                'name' => 'MiniGame',
                'name_mm' => 'အခြားဂိမ်းများ',
                'code' => '5',
                'order' => '6',
                'status' => 0,
                'img' => 'other.png',
            ],

            [
                'name' => 'FishArcade',
                'name_mm' => 'အခြားဂိမ်းများ',
                'code' => '6',
                'order' => '7',
                'status' => 0,
                'img' => 'other.png',
            ],

            [
                'name' => 'CashBonus',
                'name_mm' => 'အခြားဂိမ်းများ',
                'code' => '101',
                'order' => '8',
                'status' => 0,
                'img' => 'other.png',
            ],
            [
                'name' => 'ShanKoMee',
                'name_mm' => 'ရှမ်းကိုးမီး',
                'code' => '1001',
                'order' => '9',
                'status' => 1,
                'img' => 'slots.png',
            ],
            [
                'name' => 'Buu Gyi',
                'name_mm' => 'ဘူကြီး',
                'code' => '1002',
                'order' => '10',
                'status' => 1,
                'img' => 'slots.png',
            ],
            [
                'name' => 'Poker13',
                'name_mm' => 'ပိုကာ',
                'code' => '1003',
                'order' => '11',
                'status' => 1,
                'img' => 'slots.png',
            ],
            [
                'name' => 'BlackJack',
                'name_mm' => '၂၁',
                'code' => '1004',
                'order' => '12',
                'status' => 1,
                'img' => 'slots.png',
            ],

        ];

        GameType::insert($data);
    }
}
