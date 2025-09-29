<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Menu;
use Illuminate\Support\Str; // ★忘れずに追加
use App\Models\Cafeteria;   // ★忘れずに追加

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

                        // 選択中の食堂情報を取得
                        $cafeteriaId = $request->session()->get('selected_cafeteria_id');
                        $cafeteria = Cafeteria::find($cafeteriaId);
            
                        // バウチャーコードのプレフィックスを決定
                        $prefix = '';
                        if ($cafeteria) {
                            // 食堂名の最初の3文字などをプレフィックスにする（例: CET, EST）
                            $prefix = strtoupper(substr($cafeteria->name, 0, 3));
                        }
            
                        // ユニークなバウチャーコードを生成
                        do {
                            $randomCode = Str::upper(Str::random(3)) . '-' . Str::upper(Str::random(3));
                            $voucherCode = $prefix . $randomCode;
                        } while (Order::where('voucher_code', $voucherCode)->exists()); // 念のため重複チェック
            
                        // 注文情報（ordersテーブル）を保存
                        $order = Order::create([
                            'user_id' => Auth::id(),
                            'total_price' => $totalPrice,
                            'status' => '調理準備中',
                            'voucher_code' => $voucherCode, // ★バウチャーコードを追加
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
        return redirect()->route('order.complete', ['order' => $order->id]);
    }

    /**
     * 注文完了画面を表示する
     */
    public function complete(Order $order)
    {
        // ログインユーザーの注文かどうかのチェック（任意だが推奨）
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        return view('order.complete', compact('order'));
    }

   // 注文履歴画面を表示する
    public function history()
    {
        // ログインしているユーザーのIDを取得
        $userId = Auth::id();

        // ユーザーIDに紐づく注文を、関連する詳細とメニュー情報も含めて取得
        // latest() で新しい順に並び替え
        $orders = Order::where('user_id', $userId)
                        ->with('details.menu') // ★リレーションを最大限活用
                        ->latest()
                        ->get();

        return view('order.history', compact('orders'));
}
}