<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            ご注文完了
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg-px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-10 text-center">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-4">
                    ご注文ありがとうございました。
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mb-8">
                    商品受け取りの際に、以下の引換番号を提示してください。
                </p>
                <p class="text-gray-600 dark:text-gray-400 mb-8">
                    ※お受け取り予定時刻から60分経過するとバウチャーが自動で無効になります。
                </p>

                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-6 my-8">
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-2">あなたのバウチャー</p>
                    <p class="text-4xl font-mono font-bold text-gray-900 dark:text-gray-100 tracking-wider">
                        {{ $order->voucher_code }}
                    </p>

                            <div class="border-t dark:border-gray-600 mt-6 pt-4">
                            <p class="text-lg text-gray-600 dark:text-gray-400 mb-2">お受け取り予定時刻</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                           {{ \Carbon\Carbon::parse($order->pickup_time)->format('m月d日 H:i') }}
                         </p>
                     </div>
                     @if ($order->payment_method)
<div class="border-t dark:border-gray-600 mt-4 pt-4">
    <p class="text-lg text-gray-600 dark:text-gray-400 mb-2">お支払い方法</p>
    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
        {{ $order->payment_method }}
    </p>
</div>
@endif
                </div>
                @if($order->status === '調理準備中')
    @php
        
        $canCancel = \Carbon\Carbon::now('Asia/Tokyo')->diffInMinutes($order->pickup_time, false) > 30;
    @endphp

    @if($canCancel)
        <div class="mt-8 pt-6 border-t dark:border-gray-600">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            キャンセルポリシー：
            受け取り予定時刻の30分前までキャンセルが可能です。
            </p>
            <form method="POST" action="{{ route('order.cancel', $order) }}" onsubmit="return confirm('本当にこの注文をキャンセルしますか？');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700">
                    この注文をキャンセルする
                </button>
            </form>
        </div>
    @else
        <div class="mt-8 pt-6 border-t dark:border-gray-600">
            <p class="text-sm text-yellow-600 dark:text-yellow-400">
                申し訳ございません。受け取り予定時刻の30分前を過ぎたため、この注文はキャンセルできません。
            </p>
        </div>
    @endif
@elseif($order->status === 'キャンセル済み')
    <div class="mt-8 pt-6 border-t dark:border-gray-600">
        <p class="text-lg font-bold text-red-600 dark:text-red-400">
            この注文はキャンセル済みです。
        </p>
    </div>
@endif
                <a href="{{ route('dashboard') }}" class="mt-8 inline-block bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600">
                    ホームに戻る
                </a>
            </div>
        </div>
    </div>
</x-app-layout>