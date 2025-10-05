<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $menu->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <img src="{{ asset($menu->image_path) }}" alt="{{ $menu->name }}" class="w-full md:w-1/2 rounded-lg mb-6"> 
                <h1 class="text-3xl font-bold mb-2 text-gray-900 dark:text-gray-100">{{ $menu->name }}</h1>
                <p class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ $menu->price }}円</p>
                <p class="text-gray-600 dark:text-gray-400 mb-2">カロリー: {{ $menu->calories ?? '---' }} kcal</p>
                <div class="mt-6 space-y-6 text-gray-700 dark:text-gray-300">
    @if ($menu->product_description)
        <div>
            <h3 class="text-lg font-bold border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-2">商品説明</h3>
            <p class="whitespace-pre-wrap">{{ $menu->product_description }}</p>
        </div>
    @endif

    @if ($menu->allergens)
        <div>
            <h3 class="text-lg font-bold border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-2">アレルゲン情報</h3>
            <p>{{ $menu->allergens }}</p>
        </div>
    @endif

    @if ($menu->nutrition_info)
        <div>
            <h3 class="text-lg font-bold border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-2">栄養成分</h3>
            <p class="whitespace-pre-wrap">{{ $menu->nutrition_info }}</p>
        </div>
    @endif
</div>
                <a href="{{ route('dashboard') }}" class="mt-6 inline-block text-blue-500 hover:underline">&laquo; メニュー一覧に戻る</a>
            </div>
        </div>
    </div>
</x-app-layout>