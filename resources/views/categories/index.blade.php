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
                        <div class="block w-52 font-bold">Kategorie: </div>
                        <div class="block">
                            <a href="{{route('categories.create')}}" class="mb-4 inline-block text-white bg-indigo-400 border-0 py-2 px-4 focus:outline-none hover:bg-indigo-500 rounded">Dodaj kategorię</a>
                        </div>
                        @foreach($categories as $category)
                            <a href="{{route('categories.show',$category->id)}}">{{$category->name}}</a> (Pytań: <b>{{$category->questions()->count()}}</b>)<br>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
