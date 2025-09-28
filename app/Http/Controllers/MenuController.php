<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Category;

class MenuController extends Controller
{
    /**
     * メニュー一覧（ホーム画面）
     */
    public function index()
    {
        // データベースから全てのカテゴリーとメニューを取得
        $categories = Category::all();
        $menus = Menu::with('category')->get(); // with('category')でN+1問題を回避

        // 'dashboard'ビューにデータを渡して表示
        return view('dashboard', compact('categories', 'menus'));
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