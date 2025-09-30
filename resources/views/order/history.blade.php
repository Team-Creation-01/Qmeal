<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            注文履歴
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @forelse ($orders as $order)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
         <div class="flex justify-between items-center border-b dark:border-gray-600 pb-4 mb-4">
           <div>
        <h3 class="text-lg font-semibold">注文日時：{{ $order->created_at->format('Y年m月d日 H:i') }}</h3>

                   @if ($order->pickup_time)
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            受け取り予定：{{ \Carbon\Carbon::parse($order->pickup_time)->format('Y年m月d日 H:i') }}
                      </p>
                       @endif
                 <p class="text-sm text-gray-600 dark:text-gray-400">注文番号：#{{ $order->id }}</p>
                   </div>
                      </div>
                            <div class="text-right">
                                <p class="text-xl font-bold">合計金額：{{ number_format($order->total_price) }}円</p>

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

                        <ul class="space-y-2">
                            @foreach ($order->details as $detail)
                                <li class="flex justify-between items-center">
                                    <span>{{ $detail->menu->name ?? '情報なし' }}</span>
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ $detail->quantity }}個 x {{ number_format($detail->price) }}円
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <p>注文履歴はまだありません。</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>



</x-app-layout>