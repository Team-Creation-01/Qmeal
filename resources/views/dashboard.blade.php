<x-app-layout>
<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $selectedCafeteria->name ?? 'メニュー一覧' }}
        </h2>
        <a href="{{ route('cafeteria.select') }}" class="text-sm text-blue-500 hover:underline">食堂を変更する</a>
    </div>
</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse ($menus as $menu)
                            <div class="border dark:border-gray-600 rounded-lg p-4 flex flex-col justify-between">
                                <div>
                                    <img src="{{ asset($menu->image_path) }}" alt="{{ $menu->name }}" class="w-full h-40 object-cover mb-4 rounded">
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
                            @empty
        {{-- データが空っぽだった場合は、このメッセージを表示 --}}
        <div class="col-span-3 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <p class="text-center">
                    こんにちは、{{ auth()->user()->name }}さん！<br>
                    ご利用になる食堂を、上の「食堂を変更する」からお選びください。
                </p>
            </div>
        </div>
        
    @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('kitchen.index')}}" class="text-sm text-blue-500 hover:underline">キッチン</a><!--追加部分　キッチンの画面に移動 -->
</x-app-layout>
