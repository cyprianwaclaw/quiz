<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pytania') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="block">
                        <div class="block w-52 font-bold mb-6">Pytania:</div>
                        <div class="block">
                            <form action="{{route('questions.update', $question)}}" method="POST">
                                @csrf
                                @method('PATCH')
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
                                    <label class="block text-gray-700 font-bold mb-2" for="question">
                                        Pytanie
                                    </label>
                                    <input
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline"
                                        id="question" name="question" type="text" value="{{ old('question') ?: $question->question }}">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-bold mb-2" for="category">
                                        Kategoria
                                    </label>
                                    <div class="inline-block relative w-64">
                                        <select
                                            class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 rounded shadow leading-tight focus:outline-none focus:shadow-outline"
                                            name="category" id="category">
                                            @foreach($categories as $category)
                                            <option value="{{$category->id}}"
                                                    {{ (old('category')==$category->id || $question->category_id == $category->id) ? "selected" : "" }}>{{$category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <button type="submit"
                                            class="inline-block text-white bg-indigo-400 border-0 py-2 px-4 focus:outline-none hover:bg-indigo-500 rounded">
                                        Zapisz
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
