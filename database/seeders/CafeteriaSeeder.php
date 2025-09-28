<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cafeteria; // ★この行を追加してください

class CafeteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cafeteria::create(['name' => 'センターゾーン食堂', 'opening_hours' => '11:00-19:00']);
        Cafeteria::create(['name' => 'イーストゾーン食堂', 'opening_hours' => '11:00-14:00']);
    }
}