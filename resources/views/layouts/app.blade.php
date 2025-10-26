<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    {{-- Alpine.jsでサイドバーの開閉状態を管理 --}}
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-pink-100 dark:bg-[#85023e] md:flex">

        {{-- サイドバー部分 (navigation.blade.php を読み込む) --}}
        {{-- モバイルでのオーバーレイ表示と閉じるボタン --}}
        <div x-show="sidebarOpen" class="fixed inset-0 flex z-40 md:hidden"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="display: none;">
            {{-- 背景のオーバーレイ --}}
            <div @click="sidebarOpen = false" class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
            {{-- サイドバー本体（navigation.blade.php が展開される） --}}
            @include('layouts.navigation')
        </div>
        {{-- PCでの常時表示 --}}
        <div class="hidden md:flex md:flex-shrink-0">
             @include('layouts.navigation')
        </div>

        {{-- メインコンテンツエリア --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- ヘッダー (ページタイトル & ハンバーガーボタン) --}}
            <header class="bg-white dark:bg-gray-800 shadow relative z-30"> {{-- z-index調整 --}}
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex items-center justify-between"> {{-- justify-between追加 --}}
                    {{-- ハンバーガーボタン (モバイル用) --}}
                    <button @click.stop="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-500 dark:text-gray-400 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {{-- ページタイトル ( $header があれば表示 ) --}}
                    <div class="flex-1 min-w-0"> {{-- タイトルが長い場合に備える --}}
                        @isset($header)
                            {{ $header }}
                        @endisset
                    </div>
                </div>
            </header>

            {{-- ページコンテンツ --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto"> {{-- 背景色はここで指定しない --}}
                <div class="container mx-auto px-6 py-8">
                     {{ $slot }}
                </div>
            </main>

            {{-- フッター --}}
            <footer class="bg-gray-100 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                    &copy; {{ date('Y') }} Qmeal. All Rights Reserved.
                </div>
            </footer>
        </div>
    </div>
</body>
</html>