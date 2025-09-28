<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('メニュー一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($menus as $menu)
                            <div class="border dark:border-gray-600 rounded-lg p-4 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-lg font-bold">{{ $menu->name }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $menu->category->name }}</p>
                                    <p class="text-lg font-semibold mt-2">{{ $menu->price }}円</p>
                                </div>
                                <div class="mt-4 flex justify-between items-center">
                                  <a href="{{ route('menu.show', $menu) }}" class="text-blue-500 hover:underline">詳細</a>
                                           <form action="{{ route('cart.add', $menu) }}" method="POST">
                                           @csrf
                                          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">カートに追加</button>
                                       </form>
                                   </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
