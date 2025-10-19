<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KitchenController; // 追加

// ------------------------------------------------------------------
// API Routes
// ------------------------------------------------------------------

// 認証が必要なAPIルートのグループ (グループを解除し、認証なしで動作確認)
// Route::middleware('auth:sanctum')->group(function () { // この行をコメントアウト

// ユーザー情報の取得（デフォルトで存在）
Route::get('/user', function (Request $request) {
    return $request->user();
});

// ★★★ 注文ステータス更新API (認証チェックを一時的に解除) ★★★
// /api/kitchen/orders/{order}/status というURLでアクセスされます
Route::post('/kitchen/orders/{order}/status', [KitchenController::class, 'updateStatus']);

// }); // この行をコメントアウト

// ------------------------------------------------------------------
