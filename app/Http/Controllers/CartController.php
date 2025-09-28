<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class CartController extends Controller
{
    /**
     * カートに商品を追加する
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

        return view('cart.index', compact('cart', 'totalPrice'));
    }
}
