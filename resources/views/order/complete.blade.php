<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            ご注文完了
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-10 text-center">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-4">
                    ご注文が成立しました。ご利用頂き、ありがとうございます。
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mb-8">
                    商品の準備ができましたら、お知らせいたします。
                </p>

                <a href="{{ route('dashboard') }}" class="mt-8 inline-block bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600">
                    ホームに戻る
                </a>
            </div>
        </div>
    </div>
</x-app-layout>