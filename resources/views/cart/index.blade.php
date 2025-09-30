<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            ショッピングカート
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if (!empty($cart))
                    <table class="w-full text-left text-gray-900 dark:text-gray-100">
                        <thead>
                            <tr class="border-b dark:border-gray-600">
                                <th class="py-2">商品名</th>
                                <th class="py-2 text-center" >価格</th>
                                <th class="py-2 text-center">数量</th>
                                <th class="py-2 text-right">小計</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cart as $id => $item)
                                <tr class="border-t dark:border-gray-700">
                                    <td class="py-4">{{ $item['name'] }}</td>
                                    <td class="text-center">{{ number_format($item['price']) }}円</td>
                                    <td class="text-center">{{ $item['quantity'] }}</td>
                                    <td class="text-right">{{ number_format($item['price'] * $item['quantity']) }}円</td>
                                    <td class="text-center"> <form action="{{ route('cart.remove', $id) }}" method="POST">
                                             @csrf
                                             @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:underline text-sm">削除</button>
                                        </form>
                                     </td> 
                                 </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="text-right mt-6">
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">合計金額: {{ number_format($totalPrice) }}円</p>
                    </div>

                    <div class="text-center mt-8">
                            <form action="{{ route('order.store') }}" method="POST">
                            @csrf
                            <div class="mb-6 text-left">
                                 <label for="pickup_time" class="block font-medium text-sm text-gray-900 dark:text-gray-100 mb-2">
                                      受け取り時刻を選択してください
                                 </label>
                             <select id="pickup_time" name="pickup_time" required class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mb-5">
                                   @foreach ($pickupTimes as $time)
                                  <option value="{{ $time['value'] }}">{{ $time['label'] }}</option>
                                    @endforeach
                                  </select>
                                </div>

                                <div class="mb-6 text-left">
        <label for="payment_method" class="block font-medium text-sm text-gray-900 dark:text-gray-100 mb-2">
            お支払い方法を選択してください
        </label>
        <select id="payment_method" name="payment_method" required class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
            <option value="PayPay">PayPay</option>
            <option value="現金">現金</option>
            <option value="クレジットカード">クレジットカード</option>
            <option value="LINE Pay">LINE Pay</option>
        </select>
    </div>
                             
                            <button type="submit" class="bg-green-500 text-white text-lg px-8 py-3 rounded-lg hover:bg-green-600">注文を確定する</button>
                        </form>
                   </div>
                @else
                    <p class="text-gray-900 dark:text-gray-100">カートは空です。</p>
                @endif
                
                <a href="{{ route('dashboard') }}" class="mt-6 inline-block text-blue-500 hover:underline">&laquo; お買い物を続ける</a>
            </div>
        </div>
    </div>
</x-app-layout>