{{-- サイドバー本体のスタイルとAlpine.js制御 --}}
{{-- PCでは常時表示、モバイルでは左からスライドイン --}}
<div class="fixed inset-y-0 left-0 w-64 bg-[#820041] text-white transform transition-transform duration-300 ease-in-out z-50 md:relative md:translate-x-0"
     :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">

    <div class="flex flex-col h-full">
        {{-- ロゴ --}}
        <div class="flex items-center justify-center h-16 border-b border-gray-700 flex-shrink-0">
            <a href="{{ route('dashboard') }}">
                {{-- ロゴコンポーネント: text-whiteを追加して色が見えるように --}}
                <x-application-logo class="block h-9 w-auto fill-current text-white" />
            </a>
        </div>

        {{-- ナビゲーションリンク --}}
        <nav class="flex-1 px-2 py-4 space-y-2 overflow-y-auto">
            {{-- nav-linkコンポーネントにクラスを追加してスタイル調整 --}}
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="block py-2.5 px-4 rounded transition duration-200">
                {{ __('ホーム') }}
            </x-nav-link>
            <x-nav-link :href="route('vouchers.index')" :active="request()->routeIs('vouchers.index')" class="block py-2.5 px-4 rounded transition duration-200">
                {{ __('バウチャー') }}
            </x-nav-link>
             {{-- マップへのリンクを追加する場合 --}}
             {{-- <x-nav-link :href="route('map.index')" :active="request()->routeIs('map.index')" class="block py-2.5 px-4 rounded transition duration-200"> --}}
             {{--     {{ __('周辺マップ') }} --}}
             {{-- </x-nav-link> --}}
            <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.index')" class="block py-2.5 px-4 rounded transition duration-200">
                {{ __('カート') }}
            </x-nav-link>
             <x-nav-link :href="route('order.history')" :active="request()->routeIs('order.history')" class="block py-2.5 px-4 rounded transition duration-200">
                 {{ __('支払い履歴') }}
             </x-nav-link>
        </nav>

        {{-- ユーザーメニュー (画面下部) --}}
        <div class="border-t border-gray-700 p-4 flex-shrink-0">
            <div x-data="{ open: false }" class="relative">
                {{-- ユーザー名表示ボタン --}}
                <button @click="open = !open" class="flex items-center w-full text-left rounded-md px-4 py-2 text-white/75 hover:bg-white/10 hover:text-white focus:outline-none">
                     {{-- アイコンを追加 --}}
                     <svg class="h-5 w-5 mr-2" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>{{ Auth::user()->name }}</span>
                     {{-- 上下矢印 --}}
                     <svg class="ml-auto h-5 w-5 transform transition-transform duration-200" :class="{'rotate-180': open, 'rotate-0': !open}" fill="currentColor" viewBox="0 0 20 20">
                         <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                     </svg>
                </button>

                {{-- ドロップダウンメニュー (上に開くように調整) --}}
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute bottom-full left-0 w-full mb-2 origin-bottom-left bg-white dark:bg-gray-700 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50" {{-- z-index調整 --}}
                     style="display: none;"
                     @click.away="open = false">
                    <div class="py-1">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('アカウント') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('ログアウト') }}
                            </x-dropdown-link>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> {{-- flex flex-col h-full の閉じタグ --}}
</div> {{-- サイドバー本体の閉じタグ --}}
