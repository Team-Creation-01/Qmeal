<?php

namespace App\Models; // ★namespace が 'App\Models' になっているか

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = []; // ★この行を追加（今後のエラー防止のため）
}