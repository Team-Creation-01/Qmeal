<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Cafeteria; // ★★★ この行を追加してください ★★★

class MenuController extends Controller
{
    /**
     * メニュー一覧（ホーム画面）
     */
    public function index()
    {
        // セッションから選択された食堂IDを取得
        $selectedCafeteriaId = session('selected_cafeteria_id');
    
        // 選択された食堂の情報を取得して、ビューで食堂名などを表示できるようにする
        $selectedCafeteria = Cafeteria::find($selectedCafeteriaId);
    
        // カテゴリーを全て取得
        $categories = Category::all();
    
        // 選択された食堂IDに紐づくメニューだけを取得
        $menus = Menu::where('cafeteria_id', $selectedCafeteriaId)
                       ->with('category')
                       ->get();

                       // dd($menus); // ★★★ この行を追加してください！ ★★★
    
        // 取得したデータをビューに渡す
        return view('dashboard', compact('categories', 'menus', 'selectedCafeteria'));
    }

    /**
     * メニュー詳細
     */
    public function show(Menu $menu) // ルートモデルバインディングを使用
    {
        // 'menu.show'ビューにデータを渡して表示
        return view('menu.show', compact('menu'));
    }
}