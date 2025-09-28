<?php

namespace App\Models; // ★この行が 'App\Models' になっているか確認

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cafeteria extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = []; // Seederでcreateを使うために、一時的にこのように設定します
}