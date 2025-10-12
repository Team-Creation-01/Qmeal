<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use Carbon\Carbon; // ★忘れずに追加

class CartController extends Controller
{
    /**
     * カートに商品を追加すると、addメソッドが呼び出される
     */
    public function add(Request $request, Menu $menu)
    {
        // セッションからカート情報を取得。なければ空の配列をデフォルト値とする
        $cart = $request->session()->get('cart', []);

        // カートに商品IDが存在するかチェック
        if (isset($cart[$menu->id])) {
            // 存在すれば、数量を1増やす
            $cart[$menu->id]['quantity']++;
        } else {
            // 存在しなければ、新しい商品としてカートに追加
            $cart[$menu->id] = [
                'name' => $menu->name,
                'price' => $menu->price,
                'quantity' => 1,
            ];
        }

        // 更新したカート情報をセッションに保存
        $request->session()->put('cart', $cart);

        // カート表示ページにリダイレクト
        return redirect()->route('cart.index')->with('success', $menu->name . 'をカートに追加しました。');
    }

    /**
     * カートの中身を表示する
     */
    public function index(Request $request)
    {
        // セッションからカート情報を取得
        $cart = $request->session()->get('cart', []);
        
        $totalPrice = 0;
        // カート内の各商品の小計と、全体の合計金額を計算
        foreach ($cart as $id => $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }

        // 受け取り時間の選択肢を生成
        $pickupTimes = [];
        // 現在時刻を基準にする（日本時間）
        $now = Carbon::now('Asia/Tokyo');

        // 開始時刻を決定（現在時刻の30分後から、15分単位で切り上げ）
        $startTime = $now->copy()->addMinutes(30);
        $remainder = $startTime->minute % 15;
        if ($remainder !== 0) {
            $startTime->addMinutes(15 - $remainder)->second(0);
        }

        // 終了時刻を決定（3日後の終わりまで）
        $endTime = $now->copy()->addDays(3)->endOfDay();

        // 15分刻みで選択肢を生成
        $currentTime = $startTime->copy();
        while ($currentTime->lte($endTime)) {
            $pickupTimes[] = [
                'value' => $currentTime->toDateTimeString(), // DB保存用の値 (例: 2025-09-30 18:45:00)
                'label' => $currentTime->format('m月d日 H:i'),   // 表示用の値 (例: 09月30日 18:45)
            ];
            $currentTime->addMinutes(15);
        }

        return view('cart.index', compact('cart', 'totalPrice','pickupTimes'));
    }

    public function remove(Request $request, $id)
    {
        // セッションからカート情報を取得
        $cart = $request->session()->get('cart', []);

        // 指定された商品IDがカートに存在するかチェック
        if (isset($cart[$id])) {
            // 存在すれば、その商品をカート配列から削除
            unset($cart[$id]);
        }

        // 更新したカート情報をセッションに保存
        $request->session()->put('cart', $cart);

        // カート表示ページにリダイレクト
        return redirect()->route('cart.index')->with('success', '商品をカートから削除しました。');
    }
}
