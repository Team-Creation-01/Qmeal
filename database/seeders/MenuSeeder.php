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
            'name' => 'ジャンボカツカレー', 'price' => 650, 'calories' => 800,
            'product_description' => '揚げたての豚カツを一枚まるごと盛り付けた、ボリューム感のあるカツカレーです。じっくり煮込んだカレーソースと、サクサクとした衣の豚カツ、そして大盛りのご飯が一体となった一皿。食べ応えを求める方に満足いただける量を提供します。',
            'allergens' => '卵、乳、小麦、鶏肉、大豆、りんご',
            'nutrition_info' => 'エネルギー: 800kcal, タンパク質: 40g, 脂質: 50g, 炭水化物: 60g, 塩分相当量: 4.5g',
            'cafeteria_id' => 1, 'category_id' => 4, 'image_path' => 'images/katsu_carry.jpeg'
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

        Menu::create([
            'name' => 'チキン南蛮定食',
            'price' => 580,
            'calories' => 850,
            'product_description' => '宮崎名物のチキン南蛮。自家製タルタルソースがたっぷりかかった、不動の人気No.1メニューです。',
            'allergens' => '卵、乳、小麦、鶏肉、大豆',
            'nutrition_info' => 'エネルギー: 850kcal, タンパク質: 40g, 脂質: 50g, 炭水化物: 60g, 塩分相当量: 4.5g',
            'cafeteria_id' => 1,
            'category_id' => 1, // 定食
            'image_path' => 'images/chicken_nanban.jpg'
        ]);

        Menu::create([
            'name' => 'カツカレー',
            'price' => 550,
            'calories' => 1100,
            'product_description' => '揚げたてのサクサクとんかつを乗せた、ボリューム満点のカレーライス。学生の強い味方です。',
            'allergens' => '小麦、豚肉、大豆、乳',
            'nutrition_info' => 'エネルギー: 1100kcal, タンパク質: 35g, 脂質: 60g, 炭水化物: 105g, 塩分相当量: 5.0g',
            'cafeteria_id' => 1,
            'category_id' => 4, // カレー
            'image_path' => 'images/katsu_curry.jpg'
        ]);
        
        // --- イーストゾーン食堂 (cafeteria_id: 2) ---
        Menu::create([
            'name' => '醤油ラーメン',
            'price' => 480,
            'calories' => 600,
            'product_description' => '昔ながらの鶏ガラスープがベースの、あっさりとした醤油ラーメン。チャーシューとメンマが乗っています。',
            'allergens' => '卵、小麦、豚肉、鶏肉、大豆',
            'nutrition_info' => 'エネルギー: 600kcal, タンパク質: 25g, 脂質: 20g, 炭水化物: 80g, 塩分相当量: 6.0g',
            'cafeteria_id' => 2,
            'category_id' => 3, // 麺類
            'image_path' => 'images/ramen.jpg'
        ]);

        Menu::create([
            'name' => 'ヘルシーサラダうどん',
            'price' => 520,
            'calories' => 450,
            'product_description' => '冷たいおうどんの上に、たっぷりの野菜と蒸し鶏を乗せました。ごまドレッシングでどうぞ。',
            'allergens' => '小麦、鶏肉、ごま、大豆',
            'nutrition_info' => 'エネルギー: 450kcal, タンパク質: 20g, 脂質: 10g, 炭水化物: 70g, 塩分相当量: 3.5g',
            'cafeteria_id' => 2,
            'category_id' => 3, // 麺類
            'image_path' => 'images/salad_udon.jpg'
        ]);

        // --- ビッグどら (cafeteria_id: 3) ---
        Menu::create([
            'name' => 'ビッグどら焼き',
            'price' => 250,
            'calories' => 400,
            'product_description' => 'その名の通り、通常の2倍の大きさのどら焼き。ふわふわの生地と甘さ控えめのあんこが特徴です。',
            'allergens' => '卵、小麦、大豆',
            'nutrition_info' => 'エネルギー: 400kcal, 炭水化物: 80g',
            'cafeteria_id' => 3,
            'category_id' => 5, // サイドメニュー
            'image_path' => 'images/big_dorayaki.jpg'
        ]);

        Menu::create([
            'name' => 'フライドポテト (L)',
            'price' => 220,
            'calories' => 510,
            'product_description' => '揚げたてアツアツのフライドポテト。勉強のお供にどうぞ。',
            'allergens' => '小麦（揚げ油に含む場合あり）',
            'nutrition_info' => 'エネルギー: 510kcal, 脂質: 24g, 炭水化物: 67g',
            'cafeteria_id' => 3,
            'category_id' => 5, // サイドメニュー
            'image_path' => 'images/french_fries.jpg'
        ]);

        // --- 理系食堂 (cafeteria_id: 4) ---
        Menu::create([
            'name' => '豚の生姜焼き定食',
            'price' => 600,
            'calories' => 900,
            'product_description' => '特製の生姜ダレで炒めた豚肉は、ご飯との相性抜群。キャベツの千切りもたっぷり添えています。',
            'allergens' => '小麦、豚肉、大豆、りんご',
            'nutrition_info' => 'エネルギー: 900kcal, タンパク質: 45g, 脂質: 55g, 炭水化物: 55g, 塩分相当量: 4.0g',
            'cafeteria_id' => 4,
            'category_id' => 1, // 定食
            'image_path' => 'images/shogayaki.jpg'
        ]);

        Menu::create([
            'name' => '天丼',
            'price' => 620,
            'calories' => 880,
            'product_description' => 'エビ、キス、かぼちゃ、ししとうなどの天ぷらを豪快にご飯の上に乗せ、甘辛いタレをかけました。',
            'allergens' => 'エビ、卵、小麦',
            'nutrition_info' => 'エネルギー: 880kcal, タンパク質: 25g, 脂質: 30g, 炭水化物: 125g',
            'cafeteria_id' => 4,
            'category_id' => 2, // 丼
            'image_path' => 'images/tendon.jpg'
        ]);
    }
}