<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cafeteria;

class CafeteriaController extends Controller
{
    /**
     * 食堂選択画面を表示する
     */
    public function select()
    {
        // データベースから全ての食堂を取得
        $cafeterias = Cafeteria::all();
        return view('cafeteria.select', compact('cafeterias'));
    }

    /**
     * 選択された食堂をセッションに保存する
     */
    public function store(Request $request)
    {
        // バリデーション：cafeteria_idが送られてきているか、DBに存在するか
        $request->validate([
            'cafeteria_id' => 'required|exists:cafeterias,id',
        ]);

        // セッションに選択された食堂のIDを保存
        $request->session()->put('selected_cafeteria_id', $request->cafeteria_id);

        // メニュー一覧ページへリダイレクト
        return redirect()->route('dashboard');
    }
}