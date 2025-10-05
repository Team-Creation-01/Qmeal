<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category; // ★この行を追加してください

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(['name' => '定食']);
        Category::create(['name' => '丼']);
        Category::create(['name' => '麺類']);
        Category::create(['name' => 'カレー']); // 追加
        Category::create(['name' => 'サイドメニュー']);
        Category::create(['name' => 'ドリンク']); // 追加
    }
}
