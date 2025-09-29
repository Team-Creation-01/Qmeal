<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // ★忘れずに追加

class Order extends Model
{
    use HasFactory;
    
    protected $guarded = []; // ★この行を追加

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
}