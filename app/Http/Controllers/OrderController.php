<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Menu;

class OrderController extends Controller
{
    /**
     * 注文をデータベースに保存する
     */
    public function store(Request $request)
    {
        // セッションからカート情報を取得
        $cart = $request->session()->get('cart', []);
        
        // カートが空の場合は、カートページにリダイレクト
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'カートが空です。');
        }

        // データベース処理中にエラーが起きたら、全てを元に戻す（トランザクション）
        DB::beginTransaction();
        try {
            // 合計金額の再計算
            $totalPrice = 0;
            foreach ($cart as $id => $item) {
                $totalPrice += $item['price'] * $item['quantity'];
            }

            // 注文情報（ordersテーブル）を保存
            $order = Order::create([
                'user_id' => Auth::id(), // ログインしているユーザーのID
                'total_price' => $totalPrice,
                'status' => '調理準備中',
            ]);

            // 注文詳細（order_detailsテーブル）を保存
            foreach ($cart as $menuId => $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'menu_id' => $menuId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'], // 保存時の価格を記録
                ]);
            }

            DB::commit(); // 全て成功したら、データベースに確定
        } catch (\Exception $e) {
            DB::rollBack(); // エラーが起きたら、処理を全て取り消し
            // エラーハンドリング（今回はシンプルに前のページに戻す）
            return redirect()->back()->with('error', '注文処理中にエラーが発生しました。もう一度お試しください。');
        }

        // カートの中身を空にする
        $request->session()->forget('cart');

        // 注文完了ページにリダイレクト
        return redirect()->route('order.complete');
    }

    /**
     * 注文完了画面を表示する
     */
    public function complete()
    {
        return view('order.complete');
    }
}