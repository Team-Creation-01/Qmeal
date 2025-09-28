<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu; // ★この行を追加してください

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // センターゾーン食堂のメニュー
        Menu::create([
            'name' => 'チキン南蛮定食', 'price' => 550, 'calories' => 800,
            'cafeteria_id' => 1, 'category_id' => 1, 'image_path' => 'images/chicken_nanban.jpg'
        ]);
        Menu::create([
            'name' => 'カツ丼', 'price' => 500, 'calories' => 750,
            'cafeteria_id' => 1, 'category_id' => 2, 'image_path' => 'images/katsudon.jpg'
        ]);
        // イーストゾーン食堂のメニュー
        Menu::create([
            'name' => '醤油ラーメン', 'price' => 480, 'calories' => 600,
            'cafeteria_id' => 2, 'category_id' => 3, 'image_path' => 'images/ramen.jpg'
        ]);
    }
}