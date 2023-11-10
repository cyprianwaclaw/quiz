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
                        <div class="block w-52 font-bold">Pytania: </div>
                        <div class="block">
                            <a href="{{route('questions.create')}}" class="mb-4 inline-block text-white bg-indigo-400 border-0 py-2 px-4 focus:outline-none hover:bg-indigo-500 rounded">Dodaj pytanie</a>
                        </div>
                        <table class="table">
                            <tbody>
                        @foreach($questions as $question)
                            <tr>
                                <td>
                            <a href="{{route('questions.show',$question->id)}}">{{$question->question}}</a></b>
                                </td>
                                <td>
                                    <a href="{{route('questions.edit', $question)}}"
                                       class="mb-1 inline-block text-white bg-indigo-400 border-0 py-1 px-2 focus:outline-none hover:bg-indigo-500 rounded">
                                        Edytuj
                                    </a>
                                    <form class="inline" action="{{route('questions.destroy',$question)}}"
                                          method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="mb-1 inline-block text-white bg-red-500 border-0 py-1 px-2 focus:outline-none hover:bg-red-600 rounded">
                                            Usuń
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
</x-app-layout>
