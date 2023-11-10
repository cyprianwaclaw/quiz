<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pytanie: '. $question->question) }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="block">
                        <div class="block w-52 font-bold">Odpowiedzi: </div>
                        <div class="block">
                            <a href="{{url('questions/' . $question->id . '/answers/create')}}" class="mb-4 inline-block text-white bg-indigo-400 border-0 py-2 px-4 focus:outline-none hover:bg-indigo-500 rounded">Dodaj odpowiedź</a>
                        </div>
                        <table class="table">
                            <tbody>
                            @foreach($question->answers as $answer)
                                <tr>
                                @if($question->correct_answer == $answer)
                                        <td><b>{{$answer->answer}}</b> <- poprawna odpowiedź</td>
                                @else
                                    <td>{{$answer->answer}}</td>
                                @endif
                                    <td>
                                        <a href="{{route('answers.edit', $answer)}}"
                                           class="mb-1 inline-block text-white bg-indigo-400 border-0 py-1 px-2 focus:outline-none hover:bg-indigo-500 rounded">
                                            Edytuj
                                        </a>
                                        <form class="inline" action="{{route('answers.destroy',$answer)}}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="mb-1 inline-block text-white bg-red-500 border-0 py-1 px-2 focus:outline-none hover:bg-red-600 rounded">
                                                Usuń
                                            </button>
                                        </form>
                                        <form class="inline" action="{{route('set_correct_asnwer',$question)}}" method="POST">
                                            @csrf
                                            <input type="hidden" name="answer_id" value="{{$answer->id}}"/>
                                            <button type="submit" class="mb-1 inline-block text-white bg-green-500 border-0 py-1 px-2 focus:outline-none hover:bg-green-600 rounded">
                                                Zaznacz jako prawidłową
                                            </button>
                                        </form>
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
{{--            <a href="{{route('quiz', $category->id)}}"--}}
{{--            class="text-white bg-indigo-400 border-0 py-2 px-4 focus:outline-none hover:bg-indigo-500 rounded">Losuj pytanie</a>--}}
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
                <tr>
{{--                    <td class="@if($question->answer->id == $question->question->correct_answer_id) text-green-600 @else text-red-600 @endif">{{$question->question->question}}</td>--}}
{{--                    <td>{{$question->answer->answer}}</td>--}}
                </tr>
{{--            @endforeach--}}
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
