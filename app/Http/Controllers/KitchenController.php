<?php

namespace App\Http\Controllers;

use App\Models\Order; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class KitchenController extends Controller
{
    // ステータス間の遷移を定義
    const STATUS_TRANSITIONS = [
        'pending' => 'cooking',
        'cooking' => 'ready',
        'ready'   => 'delivered',
        'delivered' => 'deleted',
    ];

    // ステータスごとの設定
    private function getStatusButtonConfigs(): array
    {
        // ★★★ 修正箇所: 'text' キーを 'next_text' キーに変更しました ★★★
        return [
            'pending' => ['next_text' => '”調理済み”にする', 'color' => 'bg-red-500 hover:bg-red-600'],
            'cooking' => ['next_text' => '”お届け済み”にする', 'color' => 'bg-yellow-500 hover:bg-yellow-600'],
            'ready'   => ['next_text' => '削除する', 'color' => 'bg-green-500 hover:bg-green-600'],
            'delivered' => ['next_text' => '削除を確定', 'color' => 'bg-gray-700 hover:bg-gray-800'],
        ];
    }


    /**
     * 【重要】注文一覧画面を表示するメソッド
     */
    public function index()
    {
        // ... (indexメソッドのダミーデータ生成は変更なし) ...
        $orders = collect([
            // 1. 調理中 (cooking) の注文
            Order::make([
                'id' => 101,
                'status' => 'cooking',
                'total_price' => 1250,
                'ordered_at' => Carbon::now()->subMinutes(20),
                'pickup_time_expected' => Carbon::now()->addMinutes(10)->format('H:i'),
                'items' => collect([
                    (object)['quantity' => 2, 'menu_item_name' => 'カツカレー', 'subtotal' => 1000],
                    (object)['quantity' => 1, 'menu_item_name' => 'サラダ', 'subtotal' => 250],
                ])
            ]),
            
            // 2. 未処理 (pending) の注文
            Order::make([
                'id' => 102,
                'status' => 'pending',
                'total_price' => 800,
                'ordered_at' => Carbon::now()->subMinutes(15),
                'pickup_time_expected' => Carbon::now()->addMinutes(15)->format('H:i'),
                'items' => collect([
                    (object)['quantity' => 1, 'menu_item_name' => 'うどん定食', 'subtotal' => 800],
                ])
            ]),
            
            // 3. お届け可能 (ready) の注文
            Order::make([
                'id' => 103,
                'status' => 'ready',
                'total_price' => 1500,
                'ordered_at' => Carbon::now()->subMinutes(5),
                'pickup_time_expected' => Carbon::now()->addMinutes(5)->format('H:i'),
                'items' => collect([
                    (object)['quantity' => 3, 'menu_item_name' => '日替わり弁当', 'subtotal' => 1500],
                ])
            ]),
        ]);

        $statusConfigs = $this->getStatusButtonConfigs();
        
        return view('kitchen.index', compact('orders', 'statusConfigs'));
    }
    
    /**
     * 注文のステータスを次の段階へ進めるAPI
     * * 【重要】ルートモデルバインディング（Order $order）ではなく、
     * ID（string $orderId）で受け取るように変更しました。
     * これにより、ダミーIDを使ったテストでも404エラーを防げます。
     */
    public function updateStatus(Request $request, string $orderId): JsonResponse
    {
        // ----------------------------------------------------
        // ★★★ 暫定措置：ダミーデータでステータス遷移をシミュレーション ★★★
        // ----------------------------------------------------
        // 実際には、ここで $orderId を使ってDBからレコードを取得し、更新します
        // 例：$order = Order::findOrFail($orderId);
        
        // 暫定的な処理: ダミーIDから現在のステータスを決定するロジック
        // (本来はDBから取ったOrderモデルの $order->status を使う)
        $currentStatus = match((int)$orderId) {
            101 => 'cooking',
            102 => 'pending',
            103 => 'ready',
            default => 'pending',
        };

        // 次のステータスを決定
        $newStatus = self::STATUS_TRANSITIONS[$currentStatus] ?? $currentStatus;

        // ----------------------------------------------------
        
        if ($newStatus === 'deleted') {
            // 削除アクション（ここではダミーのため実際には何もしない）
            Log::info("Order ID: {$orderId} は論理的に削除されました。");
            
            return response()->json([
                'action' => 'deleted',
                'message' => '注文が正常にリストから削除されました。'
            ]);
        }
        
        // 新しいステータスに対応するボタン設定を取得
        $config = $this->getStatusButtonConfigs()[$newStatus] ?? null;

        if (!$config) {
            return response()->json([
                'action' => 'error',
                'message' => '無効なステータス遷移です。'
            ], 400);
        }
        
        // 実際はDBを更新: 
        // $order->status = $newStatus;
        // $order->save();

        Log::info("Order ID: {$orderId} のステータスが {$currentStatus} から {$newStatus} に更新されました。");

        return response()->json([
            'action' => 'updated',
            'new_status' => $newStatus,
            'status_text' => $config['next_text'], // 次のアクションのテキスト
            'status_color' => $config['color'],
            'message' => 'ステータスが正常に更新されました。'
        ]);
    }
}
