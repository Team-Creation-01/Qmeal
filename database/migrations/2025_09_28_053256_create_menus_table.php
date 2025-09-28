<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // メニュー名
            $table->text('description')->nullable(); // 商品説明
            $table->integer('price'); // 価格
            $table->string('image_path')->nullable(); // 写真のパス
            $table->integer('calories')->nullable(); // カロリー
            $table->foreignId('cafeteria_id')->constrained()->onDelete('cascade'); // 食堂ID
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // カテゴリーID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
