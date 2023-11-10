<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Szczegóły użytkownika') .' '. $user->name}}
        </h2>
    </x-slot>

    @if(Session::has('error'))
        <div id="alert-border-2" class="flex p-4 mb-4 bg-red-100 border-t-4 border-red-500 dark:bg-red-200" role="alert">
            <svg class="flex-shrink-0 w-5 h-5 text-red-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
            <div class="ml-3 text-sm font-medium text-red-700">
                {{Session::get('error')}}
            </div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-red-100 dark:bg-red-200 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 dark:hover:bg-red-300 inline-flex h-8 w-8"  data-dismiss-target="#alert-border-2" aria-label="Close">
                <span class="sr-only">Dismiss</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
        </div>
    @endif
    @if(Session::has('success'))
        <div id="alert-border-3" class="flex p-4 mb-4 bg-green-100 border-t-4 border-green-500 dark:bg-green-200" role="alert">
            <svg class="flex-shrink-0 w-5 h-5 text-green-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
            <div class="ml-3 text-sm font-medium text-green-700">
                {{Session::get('success')}}
            </div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-green-100 dark:bg-green-200 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 dark:hover:bg-green-300 inline-flex h-8 w-8"  data-dismiss-target="#alert-border-3" aria-label="Close">
                <span class="sr-only">Dismiss</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
        </div>
    @endif

    @can('activate quiz')
        <div class="mt-4 flex flex-wrap">
            <div class="w-full md:w-1/2 px-4 mb-4">
                <div
                    class="sm:rounded-lg bg-gradient-to-r bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-2 sm:p-6 overflow-hidden">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <div class="block text-white text-xl font-bold text-center pb-3">Operacje</div>
                        <div class="flex flex-row">

                            <!-- Modal toggle -->
                            <button class="bg-sky-500 text-white active:bg-sky-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150" type="button" data-modal-toggle="give-premium-modal">
                                Przyznaj premium
                            </button>
                        </div>
                    </div>

                </div>
            </div>
            <div class="w-full md:w-1/2 px-4">
                <div
                    class="sm:rounded-lg bg-gradient-to-r bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-2 sm:p-6 overflow-hidden">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <div class="block text-white text-xl font-bold text-center pb-3">Podstawowe informacje</div>
                        <table class="table-auto w-full text-sm text-white">
                            <tbody>
                            <tr>
                                <td></td>
                                <td>{{$user->avatar_path}}</td>
                            </tr>
                            <tr>
                                <td>Imię</td>
                                <td>{{$user->name}}</td>
                            </tr>
                            <tr>
                                <td>Nazwisko</td>
                                <td>{{$user->surname}}</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>{{$user->email}}</td>
                            </tr>
                            <tr>
                                <td>Telefon</td>
                                <td>{{$user->phone}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <div class="w-full md:w-1/2 mb-4 px-4">
                <div
                    class="sm:rounded-lg bg-gradient-to-r bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-2 sm:p-6 overflow-hidden">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <div class="block text-white text-xl font-bold text-center pb-3">Dodane Quizy</div>
                    </div>
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead
                            class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="p-4 hidden">
                                <div class="flex items-center">
                                    <input id="checkbox-all-search" type="checkbox"
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="checkbox-all-search" class="sr-only">checkbox</label>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Quiz
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($user->quizzes as $quiz)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="w-4 p-4 hidden">
                                    <div class="flex items-center">
                                        <input id="checkbox-table-search-1" type="checkbox"
                                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                                    </div>
                                </td>
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                    {{$quiz->title}}
                                </th>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Main modal -->
        <div id="give-premium-modal" tabindex="-1" aria-hidden="true" class="flex overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center hidden">
            <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-toggle="give-premium-modal">
                        <svg class="w-5 h-5 pointer-events-none" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </button>
                    <div class="py-6 px-6 lg:px-8">
                        <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white">Przyznaj użytkownikowi premium</h3>
                        <form class="space-y-6" id="give-premium-form" onsubmit="event.preventDefault(); validateGivePremiumForm(event);">
                            <div>
                                <label for="days" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Ilość dni</label>
                                <input type="number" name="days" id="days" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="7" required min="1" value="7">
                                <input type="hidden" name="user_id" value="{{$user->id}}">
                            </div>
                            <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Przyznaj premium</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="modal-backdrop" class="hidden bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40"></div>
        @push('scripts')
            <script>
                let modal_backdrop = document.getElementById('modal-backdrop');
                let buttons = document.querySelectorAll('button[data-modal-toggle]');
                buttons.forEach(function (element, index) {
                    element.addEventListener('click', function (e) {
                        let target_modal = document.getElementById(e.target.getAttribute('data-modal-toggle'));
                        target_modal.classList.toggle('hidden');
                        if (!target_modal.classList.contains('hidden')) {
                            modal_backdrop.classList.remove('hidden');
                        } else {
                            modal_backdrop.classList.add('hidden');
                        }
                    });
                });

                function validateGivePremiumForm(event){
                    // data to be sent to the POST request
                    console.log(event.target.querySelector('input#days').value);
                    let _data = {
                        days: event.target.querySelector('input#days').value,
                        user_id: event.target.querySelector('input[name="user_id"]').value,
                    }

                    fetch('/api/user/givePremium', {
                        method: "POST",
                        body: JSON.stringify(_data),
                        headers: {"Content-type": "application/json; charset=UTF-8"}
                    })
                        .then(response => response.json())
                        .then(function(json){

                            new Noty({
                                type: "success",
                                text: json.data,
                                layout: "center",
                                timeout: 2000,
                                killer: true
                            }).show();
                            document.getElementById('give-premium-modal').classList.add('hidden');
                            modal_backdrop.classList.add('hidden');
                        })
                        .catch(err => console.log(err));
                }
            </script>
        @endpush

    @endcan
</x-app-layout>
