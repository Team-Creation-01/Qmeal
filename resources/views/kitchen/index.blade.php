<x-app-layout>
<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $selectedCafeteria->name ?? '注文一覧' }}
        </h2>
    </div>
</x-slot>

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 py-12">
@foreach ($orders as $order) 
    @php
        // コントローラーから渡される $statusConfigs (配列) を使用
        // $order->status が 'pending', 'cooking', 'ready' のいずれかであることを想定
        $config = $statusConfigs[$order->status];
    @endphp

    {{-- カード全体 --}}
    <div id="order-card-{{ $order->id }}" 
         data-current-status="{{ $order->status }}"
         class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition duration-300"
         style="transition-property: opacity, transform;"> 
        
        <div class="p-6 text-gray-900 dark:text-gray-100">

            {{-- カードヘッダー：注文日時やステータス --}}
            <div class="flex justify-between items-start border-b dark:border-gray-700 pb-4 mb-4">
                <div>
                    <p class="font-semibold">注文日時：{{ $order->ordered_at->format('Y年m月d日 H:i') }} </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{-- ★ 受け取り予定時刻のプロパティ名は環境に合わせてください ★ --}}
                        受け取り予定：{{ $order->pickup_time_expected ?? '未設定' }} 
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">注文番号：#{{ $order->id }}</p>
                </div>
            </div>

            {{-- 注文内容リスト (itemsリレーションを使用) --}}
            <div class="mb-4 space-y-2">
                <p class="font-bold text-sm text-indigo-500">注文内容:</p>
                <ul>
                    @foreach ($order->items as $item)
                        <li class="flex justify-between text-sm">
                            <span>{{ $item->quantity }} x {{ $item->menu_item_name }}</span>
                            <span class="font-medium">¥{{ number_format($item->subtotal) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>


            {{-- ステータスボタン --}}
            <div class="flex justify-end pt-4">
                {{-- type="button" に変更し、カスタムクラスとデータ属性を追加 --}}
                <button type="button" 
                        data-order-id="{{ $order->id }}"
                        data-current-status="{{ $order->status }}"
                        class="status-update-button text-white font-bold py-3 px-6 rounded-lg shadow-md transition duration-150 ease-in-out text-base
                               {{ $config['color'] }}">
                    {{ $config['next_text'] }}
                </button>
            </div>
            
        </div>
    </div>
@endforeach
</div>

{{-- ---------------------------------------------------- --}}
{{-- JavaScript: AJAXでステータスを更新するロジックをここに記述 --}}
{{-- ---------------------------------------------------- --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bladeから渡されたPHPのステータス設定をJavaScriptで再現
        // deliveredの次は「削除」アクションであることをJavaScriptにも理解させる
        const statusTransitions = {
            'pending': { next_text: '”調理済み”にする', color: 'bg-red-500 hover:bg-red-600' },
            'cooking': { next_text: '”お届け済み”にする', color: 'bg-yellow-500 hover:bg-yellow-600' },
            'ready':   { next_text: '削除する', color: 'bg-green-500 hover:bg-green-600' },
            'delivered': { next_text: '削除を確定', color: 'bg-gray-700 hover:bg-gray-800' } // 削除アクションのボタン
        };

        // すべてのステータス更新ボタンにイベントリスナーを設定
        document.querySelectorAll('.status-update-button').forEach(button => {
            button.addEventListener('click', async function(event) {
                event.preventDefault();
                const buttonElement = this;
                const orderId = buttonElement.dataset.orderId;
                const orderCard = document.getElementById(`order-card-${orderId}`);
                
                // ロード状態の管理のための準備
                const originalText = buttonElement.textContent;
                buttonElement.disabled = true;
                buttonElement.textContent = '更新中...';
                
                // 元の色クラスを取得してローディング色に一時的に変更
                // ここでcurrentColorClassMatchが取得できない場合を考慮し、安全に処理
                const currentColorClassMatch = buttonElement.className.match(/(bg-[a-z]+-\d+ hover:bg-[a-z]+-\d+)/);
                const currentColorClass = currentColorClassMatch ? currentColorClassMatch[0] : '';
                
                if (currentColorClass) {
                    // クラス名が複数ある可能性があるため、スプレッド構文で除去
                    buttonElement.classList.remove(...currentColorClass.split(' ')); 
                }
                buttonElement.classList.add('bg-gray-400');
                
                try {
                    // APIエンドポイントへのURL (routes/api.php で定義したパス)
                    const apiUrl = `/api/kitchen/orders/${orderId}/status`;

                    // PATCHメソッドでリクエストを送信
                    const response = await fetch(apiUrl, {
                        method: 'Post', // PATCHメソッドを使用
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        // サーバー側で次のステータスを計算するため、ボディは空でOK
                        body: JSON.stringify({}) 
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(`HTTPエラー! ステータス: ${response.status}. ${errorData.message || '不明なエラー'}`);
                    }

                    const data = await response.json();

                    if (data.action === 'deleted') {
                        // 削除が完了した場合 
                        
                        // カードをフェードアウトして削除
                        orderCard.style.opacity = '0';
                        orderCard.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            orderCard.remove();
                        }, 300);

                    } else if (data.action === 'updated') {
                        // ステータスが更新された場合 (pending -> cooking -> ready -> delivered)
                        
                        // 1. ボタンのスタイルとテキストを更新
                        buttonElement.classList.remove('bg-gray-400'); // ローディング色を削除
                        // サーバーから返された新しい色を適用
                        buttonElement.classList.add(...data.status_color.split(' ')); 
                        buttonElement.textContent = data.status_text; // 新しいテキストを設定

                        // 2. カードのデータ属性を更新（次のクリックのために重要）
                        orderCard.dataset.currentStatus = data.new_status;

                        // 3. 画面上のステータス表示（もしあれば）を更新
                        // ★★★ 注意: 画面内にステータス表示用の要素がないため、ここでは更新していません。
                        
                    } else {
                        throw new Error('サーバーからの応答が不正です。');
                    }

                } catch (error) {
                    console.error('更新エラー:', error);
                    
                    // エラー時はボタンを元の状態に戻す
                    buttonElement.classList.remove('bg-gray-400');
                    if (currentColorClass) {
                        buttonElement.classList.add(...currentColorClass.split(' '));
                    }
                    buttonElement.textContent = originalText + ' (失敗)';
                } finally {
                    // 処理終了時: ボタンを再度有効化（削除アクションでない場合のみ）
                    if (buttonElement.parentNode && orderCard.parentNode) {
                        buttonElement.disabled = false;
                    }
                }
            });
        });
    });
</script>
</x-app-layout>
