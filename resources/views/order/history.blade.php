<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            注文履歴
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        @forelse ($orders as $order)
    {{-- 注文一つ分を囲むカード --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 text-gray-900 dark:text-gray-100">

            {{-- カードヘッダー：注文日時やステータス --}}
            <div class="flex justify-between items-start border-b dark:border-gray-700 pb-4 mb-4">
                <div>
                    <p class="font-semibold">注文日時：{{ $order->created_at->format('Y年m月d日 H:i') }}</p>
                    @if ($order->pickup_time)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            受け取り予定：{{ \Carbon\Carbon::parse($order->pickup_time)->format('Y年m月d日 H:i') }}
                        </p>
                    @endif
                    <p class="text-sm text-gray-600 dark:text-gray-400">注文番号：#{{ $order->id }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    @if($order->status === 'キャンセル済み')
                        <span class="text-sm font-bold bg-red-100 text-red-800 px-2 py-1 rounded-full dark:bg-red-900 dark:text-red-300">
                            キャンセル済み
                        </span>
                    @elseif($order->status === '受け取り済み')
                        <span class="text-sm font-bold bg-green-100 text-green-800 px-2 py-1 rounded-full dark:bg-green-900 dark:text-green-300">
                            受け取り済み
                        </span>
                    @endif
                </div>
            </div>

            {{-- カードボディ：商品リスト --}}
            <div class="mb-4">
                <ul class="space-y-2">
                    @foreach ($order->details as $detail)
                        <li class="flex justify-between items-center">
                            <span class="font-medium">{{ $detail->menu->name ?? '情報なし' }}</span>
                            <span class="text-gray-600 dark:text-gray-400 text-sm">
                                {{ $detail->quantity }}個 x {{ number_format($detail->price) }}円
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- カードフッター：合計金額 --}}
            <div class="flex justify-end items-center border-t dark:border-gray-700 pt-4">
                <span class="text-gray-600 dark:text-gray-400 mr-2">合計金額:</span>
                <span class="text-xl font-bold">{{ number_format($order->total_price) }}円</span>
            </div>

        </div>
    </div>
@empty
    {{-- 注文履歴がない場合の表示 --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <p>注文履歴はまだありません。</p>
        </div>
    </div>
@endforelse
        </div>
    </div>



</x-app-layout>