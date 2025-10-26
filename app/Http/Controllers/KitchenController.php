<?php

namespace App\Http\Controllers;

use App\Models\Order; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class KitchenController extends Controller
{
    /**
     * ステータス間の遷移を、OrderControllerの日本語ステータスに合わせて定義
     */
    const STATUS_TRANSITIONS = [
        '調理準備中' => '調理中',
        '調理中'   => '準備完了',
        '準備完了'  => '受け渡し済み',
        '受け渡し済み' => 'deleted', // 'deleted'はリストから削除するための内部的な合図
    ];

    /**
     * ボタンに表示するテキストと色を、現在の日本語ステータスに基づいて決定する
     */
    private function getButtonConfig(string $currentStatus): array
    {
        switch ($currentStatus) {
            case '調理準備中':
                return ['next_text' => '”調理中”にする', 'color' => 'bg-red-500 hover:bg-red-600'];
            case '調理中':
                return ['next_text' => '”準備完了”にする', 'color' => 'bg-yellow-500 hover:bg-yellow-600'];
            case '準備完了':
                return ['next_text' => '”受け渡し済み”にする', 'color' => 'bg-green-500 hover:bg-green-600'];
            case '受け渡し済み':
                 return ['next_text' => 'リストから削除', 'color' => 'bg-gray-700 hover:bg-gray-800'];
            default:
                return ['next_text' => 'ステータス不明', 'color' => 'bg-gray-400'];
        }
    }

    /**
     * 注文一覧画面を表示する（データベースから実データを取得）
     */
    public function index()
    {
        // ★ ダミーデータを削除し、実際のDBクエリに置き換え ★
        // キッチンで対応すべきステータスの注文のみを取得
        $activeStatuses = ['調理準備中', '調理中', '準備完了'];
        
        $orders = Order::whereIn('status', $activeStatuses)
                       ->orderBy('ordered_at', 'asc') // 注文が早い順
                       ->with('items.menu') // 注文商品とメニュー情報も一緒に取得
                       ->get();

        // Bladeに渡す $buttonConfigs を生成
        $buttonConfigs = [];
        foreach ($orders as $order) {
            $buttonConfigs[$order->id] = $this->getButtonConfig($order->status);
        }
        
        return view('kitchen.index', compact('orders', 'buttonConfigs'));
    }
    
    /**
     * 注文のステータスを次の段階へ進めるAPI（データベースを実際に更新）
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        //ルートモデルバインディング（Order $order）を使用 ★
        // これにより、$orderにはデータベースから取得した実際の注文レコードが入ります。
        
        $currentStatus = $order->status;
        
        // 次のステータスを決定
        $newStatus = self::STATUS_TRANSITIONS[$currentStatus] ?? $currentStatus;
        
        // 次のステータスが 'deleted' の場合
        if ($newStatus === 'deleted') {
            // ここではDBから削除せず、単にリストから消すための応答を返す
            // もしDBからも消したい場合は $order->delete(); を実行
            Log::info("Order ID: {$order->id} はキッチン画面から削除されました。");
            
            return response()->json([
                'action' => 'deleted',
                'message' => '注文が正常にリストから削除されました。'
            ]);
        }
        
        // ★ データベースのステータスを更新 ★
        $order->status = $newStatus;
        $order->save();

        Log::info("Order ID: {$order->id} のステータスが {$currentStatus} から {$newStatus} に更新されました。");
        
        // 遷移後のステータスに対応する、次のボタン設定を取得
        $config = $this->getButtonConfig($newStatus);

        return response()->json([
            'action' => 'updated',
            'new_status' => $newStatus,
            'status_text' => $config['next_text'], // 次のアクションのテキスト
            'status_color' => $config['color'],
            'message' => 'ステータスが正常に更新されました。'
        ]);
    }
}

