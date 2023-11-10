<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kategoria: '. $category->name) }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="block">
                        <div class="block w-52 font-bold">Pytania: </div>
                        <div class="block">
                            <a href="{{route('questions.create',['category' => $category->id])}}" class="mb-4 inline-block text-white bg-indigo-400 border-0 py-2 px-4 focus:outline-none hover:bg-indigo-500 rounded">Dodaj pytanie</a>
                        </div>
                        <table class="table">
                            <tbody>
                            @foreach($questions as $question)
                                <tr>
                                    <td>{{$question->question}}</td>
                                    <td>Poprawna odpowiedź:
                                        <b>{{$question->correct_answer->answer ?? ''}}</b>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="pb-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded shadow flex p-6">
            <a href="{{route('quiz', $category->id)}}"
            class="text-white bg-indigo-400 border-0 py-2 px-4 focus:outline-none hover:bg-indigo-500 rounded">Losuj pytanie</a>
        </div>
    </div>
    <div class="pb-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded shadow flex p-6">
            <div class="block w-52 font-bold">Twoje odpowiedzi:</div>
            <table class="table">
                <tbody>
{{--                {{$user_questions[2]->question->question}}--}}
{{--                {{$user_questions[2]->answer}}--}}
{{--                @dd($user_questions)--}}
{{--            @foreach($user_questions as $question)--}}
{{--                <tr>--}}
{{--                    <td class="@if($question->answer->id == $question->question->correct_answer_id) text-green-600 @else text-red-600 @endif">{{$question->question->question}}</td>--}}
{{--                    <td>{{$question->answer->answer}}</td>--}}
{{--                </tr>--}}
{{--            @endforeach--}}
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
