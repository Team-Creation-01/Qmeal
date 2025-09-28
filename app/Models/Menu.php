<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $guarded = [];

    // ★★★★★ ここから追加 ★★★★★
    /**
     * このメニューが属するカテゴリーを取得
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    // ★★★★★ ここまで追加 ★★★★★
}