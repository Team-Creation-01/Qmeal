<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            有効なバウチャー
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @forelse ($vouchers as $voucher)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">引換番号</p>
                            <p class="text-3xl font-mono font-bold tracking-wider">{{ $voucher->voucher_code }}</p>
                        </div>
                        <div class="mt-4">
                            <p><span class="font-bold">受け取り場所:</span> {{ $voucher->cafeteria->name ?? '不明' }}</p>
                            <p><span class="font-bold">受け取り予定:</span> {{ \Carbon\Carbon::parse($voucher->pickup_time)->format('m月d日 H:i') }}</p>
                        </div>
                        <div class="mt-6 text-center">
                            <form method="POST" action="{{ route('vouchers.complete', $voucher) }}" onsubmit="return confirm('この引換券を使用済みにします。よろしいですか？');">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                                    受け取り済みにする
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <p>現在、有効なバウチャーはありません。追加するには、ホームからメニューを注文します。</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>