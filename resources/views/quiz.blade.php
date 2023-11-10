<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kategoria: ' . $category->name) }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="block">
                        <div class="inline-block w-52">Wylosowane pytanie:</div>
                        @if(isset($question))
                            <div class="inline-block font-bold">
                                <b>{{$question->question}}</b>
                            </div>
                            <div class="block">
                                @foreach($question->answers as $answer)
                                    <form action="{{route('quiz',$category->id)}}" method="POST" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="answer" value="{{$answer->id}}">
                                        <button type="submit"
                                                class="p-2 border bg-indigo-400">{{$answer->answer}}</button>

                                    </form>
                                @endforeach
                            </div>
                        @else
                            <div class="inline-block font-bold">
                                <b>Brak dostępnych pytań w tej kategorii</b>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
