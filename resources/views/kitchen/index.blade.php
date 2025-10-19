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
        // ★ 修正箇所: $statusConfigs -> $buttonConfigs[$order->id] に変更 ★
        // コントローラーで注文IDごとに生成した初期ボタン設定を使用
        $config = $buttonConfigs[$order->id];
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
                    {{-- 現在のステータスを表示 --}}
                    <p class="text-xs font-bold mt-1 
                        {{ $order->status === 'pending' ? 'text-red-500' : 
                          ($order->status === 'cooking' ? 'text-yellow-500' : 
                          ($order->status === 'ready' ? 'text-green-500' : 'text-gray-500')) }}"
                        id="status-text-{{ $order->id }}">
                        現在のステータス: {{ $order->status }}
                    </p>
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
        // CSRFトークンを取得（Laravelの標準的な方法）
        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';

        // ステータス表示のクラスを更新するヘルパー関数
        function updateStatusDisplay(statusElement, newStatus) {
            statusElement.textContent = `現在のステータス: ${newStatus}`;
            statusElement.classList.remove('text-red-500', 'text-yellow-500', 'text-green-500', 'text-gray-500');
            
            if (newStatus === 'pending') statusElement.classList.add('text-red-500');
            else if (newStatus === 'cooking') statusElement.classList.add('text-yellow-500');
            else if (newStatus === 'ready') statusElement.classList.add('text-green-500');
            else statusElement.classList.add('text-gray-500');
        }

        // すべてのステータス更新ボタンにイベントリスナーを設定
        document.querySelectorAll('.status-update-button').forEach(button => {
            button.addEventListener('click', async function(event) {
                event.preventDefault();
                const buttonElement = this;
                const orderId = buttonElement.dataset.orderId;
                const orderCard = document.getElementById(`order-card-${orderId}`);
                const statusElement = orderCard.querySelector(`#status-text-${orderId}`); // ステータス表示要素を取得
                
                // ロード状態の管理のための準備
                const originalText = buttonElement.textContent;
                buttonElement.disabled = true;
                buttonElement.textContent = '更新中...';
                
                // 元の色クラスを安全に取得
                const colorClasses = buttonElement.className.match(/(bg-[a-z]+-\d+ hover:bg-[a-z]+-\d+)/g) || [];
                const currentColorClass = colorClasses.join(' '); 
                
                if (currentColorClass) {
                    buttonElement.classList.remove(...currentColorClass.split(' ')); 
                }
                buttonElement.classList.add('bg-gray-400', 'hover:bg-gray-500'); // ローディング色に一時的に変更
                
                try {
                    // APIエンドポイントへのURL 
                    const apiUrl = `/api/kitchen/orders/${orderId}/status`; 

                    const response = await fetch(apiUrl, {
                        method: 'POST', 
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken, 
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({}) // サーバー側で次のステータスを計算
                    });

                    const data = await response.json();

                    if (!response.ok) {
                         throw new Error(`エラー: ${data.message || 'ステータス更新に失敗しました。'}`);
                    }


                    if (data.action === 'deleted') {
                        // 削除が完了した場合 
                        
                        // カードをフェードアウトして削除
                        orderCard.style.opacity = '0';
                        orderCard.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            orderCard.remove();
                        }, 300);

                    } else if (data.action === 'updated') {
                        // ステータスが更新された場合
                        
                        // 1. ボタンのスタイルとテキストを更新
                        buttonElement.classList.remove('bg-gray-400', 'hover:bg-gray-500'); // ローディング色を削除
                        buttonElement.classList.add(...data.status_color.split(' ')); // サーバーから返された新しい色を適用
                        buttonElement.textContent = data.status_text; // 新しいテキストを設定

                        // 2. カードのデータ属性を更新
                        orderCard.dataset.currentStatus = data.new_status;

                        // 3. 現在のステータス表示を更新
                        if (statusElement) {
                            updateStatusDisplay(statusElement, data.new_status);
                        }
                        
                    } else {
                        throw new Error('サーバーからの応答が不正です。');
                    }

                } catch (error) {
                    console.error('更新エラー:', error);
                    // alert(error.message); // エラーメッセージ表示（環境による制限があれば外す）
                    
                    // エラー時はボタンを元の状態に戻す
                    buttonElement.classList.remove('bg-gray-400', 'hover:bg-gray-500');
                    if (currentColorClass) {
                        buttonElement.classList.add(...currentColorClass.split(' '));
                    }
                    buttonElement.textContent = originalText + ' (再試行)';
                } finally {
                    // 処理終了時: ボタンを再度有効化（削除アクションでない場合のみ）
                    if (orderCard && orderCard.parentNode) {
                        buttonElement.disabled = false;
                    }
                }
            });
        });
    });
</script>
</x-app-layout>