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
use Carbon\Carbon; // ★忘れずに追加

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
            
                        // バリデーション：受け取り時刻が必須かつ日付形式であることを確認
                         // バリデーションルールを修正
        $request->validate([
            'pickup_time' => 'required|date',
            'payment_method' => 'required|string|in:PayPay,現金,クレジットカード,LINE Pay', // ★この行を修正・追加
        ]);
        
                        // 注文情報（ordersテーブル）を保存
                        $order = Order::create([
                            'user_id' => Auth::id(),
                            'cafeteria_id' => $request->session()->get('selected_cafeteria_id'), // ★この行を追加
                            'total_price' => $totalPrice,
                            'status' => '調理準備中',
                            'voucher_code' => $voucherCode, // ★バウチャーコードを追加\
                            'pickup_time' => $request->pickup_time, // ★この行を追加
                            'payment_method' => $request->payment_method, // ★この行を追加
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

    //注文をキャンセルする
public function cancel(Order $order)
{
   // 1. 本人の注文かどうかの確認
   if ($order->user_id !== Auth::id()) {
       abort(403, '不正な操作です。');
   }

   // 2. 既にキャンセル済みでないか確認
   if ($order->status !== '調理準備中') {
       return redirect()->route('order.history')->with('error', 'この注文はすでに処理済みのため、キャンセルできません。');
   }

   // 3. キャンセル可能な時間内か確認
   $now = Carbon::now('Asia/Tokyo');
   if ($now->diffInMinutes($order->pickup_time, false) <= 30) {
       return redirect()->back()->with('error', '受け取り時刻の30分前を過ぎたため、キャンセルできません。');
   }

   // 4. 全てのチェックをパスしたら、ステータスを更新
   $order->status = 'キャンセル済み';
   $order->save();

   // 注文履歴ページにリダイレクト
   return redirect()->route('order.history')->with('success', '注文をキャンセルしました。');
}

        public function vouchers()
         {
          // ログインユーザーの、ステータスが「調理準備中」の注文のみ取得
         $vouchers = Order::where('user_id', Auth::id())
                        ->where('status', '調理準備中')
                        ->with('cafeteria') // 食堂情報も一緒に取得
                        ->orderBy('pickup_time', 'asc') // 受け取り時刻が近い順に並び替え
                        ->get();

      return view('order.vouchers', compact('vouchers'));
   }

   public function markAsCompleted(Order $order)
{
    // 本人の注文かどうかの確認
    if ($order->user_id !== Auth::id()) {
        abort(403, '不正な操作です。');
    }

    // ステータスを更新
    $order->status = '受け取り済み';
    $order->save();

    // バウチャー一覧ページにリダイレクト
    return redirect()->route('vouchers.index')->with('success', '引換券を使用済みにしました。');
}
}