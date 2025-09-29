<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            こんにちは！ご利用になる食堂を選択してください。
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('cafeteria.store') }}">
                        @csrf

                        <div>
                            <x-input-label for="cafeteria_id" :value="__('食堂')" />
                            <select id="cafeteria_id" name="cafeteria_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">選択してください</option>
                                @foreach ($cafeterias as $cafeteria)
                                    <option value="{{ $cafeteria->id }}">{{ $cafeteria->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('cafeteria_id')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('決定') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>