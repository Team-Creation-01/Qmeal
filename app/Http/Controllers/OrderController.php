<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cafeteria;
use App\Models\OrderDetail;
use App\Models\Menu;
use App\Services\PayPayService; // PayPayService
use Illuminate\Http\Request;    // Request
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * 注文をデータベースに保存し、決済処理を開始する
     */
    public function store(Request $request)
    {
        // (1) バリデーションをメソッドの最初に移動
        $request->validate([
            'pickup_time' => 'required|date',
            'payment_method' => 'required|string|in:PayPay,現金,クレジットカード,LINE Pay',
        ]);

        $cart = $request->session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'カートが空です。');
        }

        // (2) データベースへの保存処理 (トランザクション)
        DB::beginTransaction();
        $order = null; // 変数を try の外で初期化
        try {
            // 合計金額の計算
            $totalPrice = 0;
            foreach ($cart as $id => $item) {
                $totalPrice += $item['price'] * $item['quantity'];
            }

            // バウチャーコードの生成
            $cafeteriaId = $request->session()->get('selected_cafeteria_id');
            $cafeteria = Cafeteria::find($cafeteriaId);
            $prefix = $cafeteria ? strtoupper(substr($cafeteria->name, 0, 3)) : 'QML';
            do {
                $randomCode = Str::upper(Str::random(3)) . '-' . Str::upper(Str::random(3));
                $voucherCode = $prefix . $randomCode;
            } while (Order::where('voucher_code', $voucherCode)->exists());

            // 注文を「支払い待ち」ステータスで作成
            $order = Order::create([
                'user_id' => Auth::id(),
                'cafeteria_id' => $cafeteriaId,
                'total_price' => $totalPrice,
                'status' => '支払い待ち', 
                'voucher_code' => $voucherCode,
                'pickup_time' => $request->pickup_time,
                'payment_method' => $request->payment_method,
            ]);

            // 注文詳細を保存
            foreach ($cart as $menuId => $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'menu_id' => $menuId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
            
            DB::commit(); // 注文DBへの保存を確定
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '注文処理中にエラーが発生しました。 ' . $e->getMessage());
        }
        
        // (3) 決済方法による分岐 (DB保存後に行う)
        
        // ★PayPayが選択された場合
        if ($request->payment_method === 'PayPay') {
            try {

                $merchantPaymentId = Str::uuid();

                //var_dump($payPayService);
                //exit;

                // 引数で受け取った $payPayService を使う
                $response = (app()->make(PayPayService::class))->createQrCode(
                    $merchantPaymentId,
                    $totalPrice,
                    'qmealでのご注文'
                );
                $qrCodeUrl = $response['data']['url'];
            
                // カートを空にする
                $request->session()->forget('cart');

                // QRコード表示用のビューにデータを渡して表示
                return view('order.payment', [
                    'order' => $order,
                    'qrCodeString' => $qrCodeUrl
                ]);

            } catch (\Exception $e) {
                // PayPayへのリクエストが失敗した場合
                $order->status = '支払い失敗'; // ステータスを更新
                $order->save();
                return redirect()->route('cart.index')->with('error', '決済の準備に失敗しました。 ' . $e->getMessage());
            }
        }

        // ★PayPay以外（現金など）が選択された場合
        $order->status = '調理準備中'; // ステータスを「調理準備中」に更新
        $order->save();
        $request->session()->forget('cart'); // カートを空にする
        
        // 従来の注文完了ページ（バウチャーページ）にリダイレクト
        return redirect()->route('order.complete', ['order' => $order->id]);
    }


    /**
     * 注文完了画面を表示する
     */
    public function complete(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        return view('order.complete', compact('order'));
    }

    /**
     * 注文履歴画面を表示する
     */
    public function history()
    {
        $userId = Auth::id();
        $orders = Order::where('user_id', $userId)
                        ->with('details.menu', 'cafeteria') // cafeteriaも追加
                        ->latest()
                        ->get();

        return view('order.history', compact('orders'));
    }

    /**
     * 注文をキャンセルする
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, '不正な操作です。');
        }
        
        // ★「支払い待ち」または「調理準備中」の注文のみキャンセル可能
        if (!in_array($order->status, ['支払い待ち', '調理準備中'])) {
            return redirect()->route('order.history')->with('error', 'この注文はすでに処理済みのため、キャンセルできません。');
        }

        // キャンセル可能な時間内か確認
        $now = Carbon::now('Asia/Tokyo');
        if ($now->diffInMinutes($order->pickup_time, false) <= 30) {
            return redirect()->back()->with('error', '受け取り時刻の30分前を過ぎたため、キャンセルできません。');
        }

        $order->status = 'キャンセル済み';
        $order->save();

        return redirect()->route('order.history')->with('success', '注文をキャンセルしました。');
    }

    /**
     * 有効なバウチャー一覧を表示する
     */
    public function vouchers()
    {
        // ★「調理準備中」の注文のみが有効な引換券
        $vouchers = Order::where('user_id', Auth::id())
                     ->where('status', '調理準備中')
                     ->with('cafeteria')
                     ->orderBy('pickup_time', 'asc')
                     ->get();

        return view('order.vouchers', compact('vouchers'));
    }

    /**
     * 注文を「受け取り済み」にする
     */
    public function markAsCompleted(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, '不正な操作です。');
        }

        $order->status = '受け取り済み';
        $order->save();

        return redirect()->route('vouchers.index')->with('success', '引換券を使用済みにしました。');
    }

    /**
     * JavaScriptからの支払いステータス確認に応答する
     */
    public function checkPaymentStatus(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        
        return response()->json(['status' => $order->status]);
    }
}