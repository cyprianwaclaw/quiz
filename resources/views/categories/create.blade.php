<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kategorie pytań') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="block">
                        <div class="block w-52 font-bold mb-6">Kategorie:</div>
                        <div class="block">
                            <form action="{{route('categories.store')}}" method="POST">
                                @csrf
                                @if ($errors->any())
                                    <div role="alert" class="mb-4">
                                        <div class="border border-red-400 rounded-b bg-red-100 px-4 py-3 text-red-700">
                                            @foreach ($errors->all() as $error)
                                                <p>{{ $error }}</p>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-bold mb-2" for="name">
                                        Nazwa
                                    </label>
                                    <input
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline"
                                        id="name" name="name" type="text" value="{{ old('name') }}">
                                </div>
                                <div>
                                    <button type="submit"
                                            class="inline-block text-white bg-indigo-400 border-0 py-2 px-4 focus:outline-none hover:bg-indigo-500 rounded">
                                        Dodaj
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
