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
        Schema::table('menus', function (Blueprint $table) {
            
            $table->dropColumn('description');

        // 新しい3つのカラムを追加
        $table->text('product_description')->nullable()->after('calories');
        $table->string('allergens')->nullable()->after('product_description');
        $table->text('nutrition_info')->nullable()->after('allergens');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            //
            // ロールバック（元に戻す）処理
        $table->dropColumn(['product_description', 'allergens', 'nutrition_info']);
        $table->text('description')->nullable(); // 古いカラムを再追加
        });
    }
};
