<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // ★忘れずに追加

class OrderDetail extends Model
{
    use HasFactory;
    
    protected $guarded = []; // ★この行を追加

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}