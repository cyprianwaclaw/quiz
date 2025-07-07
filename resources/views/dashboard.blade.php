<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @if(Session::has('error'))
    <div id="alert-border-2" class="flex p-4 mb-4 bg-red-100 border-t-4 border-red-500 dark:bg-red-200" role="alert">
        <svg class="flex-shrink-0 w-5 h-5 text-red-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
        </svg>
        <div class="ml-3 text-sm font-medium text-red-700">
            {{Session::get('error')}}
        </div>
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-red-100 dark:bg-red-200 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 dark:hover:bg-red-300 inline-flex h-8 w-8" data-dismiss-target="#alert-border-2" aria-label="Close">
            <span class="sr-only">Dismiss</span>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
    </div>
    @endif
    @if(Session::has('success'))
    <div id="alert-border-3" class="flex p-4 mb-4 bg-green-100 border-t-4 border-green-500 dark:bg-green-200" role="alert">
        <svg class="flex-shrink-0 w-5 h-5 text-green-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
        </svg>
        <div class="ml-3 text-sm font-medium text-green-700">
            {{Session::get('success')}}
        </div>
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-green-100 dark:bg-green-200 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 dark:hover:bg-green-300 inline-flex h-8 w-8" data-dismiss-target="#alert-border-3" aria-label="Close">
            <span class="sr-only">Dismiss</span>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
    </div>
    @endif
    <div class="py-4">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="block">
                        <div class="inline-block w-52">Twoje punkty:</div>
                        <div class="inline-block font-bold">
                            <b>{{auth()->user()->points}}</b>
                        </div>
                    </div>
                    <div class="block">
                        <div class="inline-block w-52">
                            You're logged in as
                        </div>
                        <div class="inline-block font-bold">
                            {{auth()->user()->name}}
                        </div>
                    </div>

                    <div class="block">
                        {{-- <div class="inline-block w-52">
                            Twój link zapraszający:
                        </div> --}}
                        <div class="inline-block font-bold">
                            <code>{{route('register')}}?invitation={{auth()->user()->invite->token}}</code>
                        </div>
                    </div>
                    <div class="block">
                        <div class="inline-block w-52">Zaproszony przez:</div>
                        @isset(auth()->user()->inviting)
                        <div class="inline-block font-bold"> {{auth()->user()->inviting->name}} </div>
                        @endisset
                        </b>
                    </div>
                    <div class="block pt-2">
                        <div class="inline-block w-52">
                            Zaprosiłeś ({{auth()->user()->invited->count()}} osób):
                        </div>

                        @foreach(auth()->user()->invited as $user)
                        @if($user->activePlanSubscriptions()->count())
                        <div class="block font-bold pl-52">{{$user->name}} PREMIUM</div>
                        @else
                        <div class="block pl-52">{{$user->name}}</div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="pb-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-white rounded shadow">
            @foreach($user_plan as $up)
            <div class="p-4">
                Twój plan: <b>{{$up->plan->id}}|{{$up->plan->name}}</b> ({{$up->plan->description}})<br>
                Data wygaśnięcia: <b>{{$up->ends_at}}</b>
            </div>
            @endforeach
        </div>
    </div>
    <div class="pb-4">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-wrap -m-4">
                        @foreach($plans as $plan)
                        <div class="w-full p-4 xl:w-1/2 md:w-1/2">
                            <div class="relative flex flex-col h-full p-6 overflow-hidden border-2 border-gray-300 rounded-lg">
                                <h2 class="mb-1 text-sm font-medium tracking-widest title-font">{{$plan->description}}</h2>
                                <h1 class="flex items-center pb-4 mb-4 text-5xl leading-none text-gray-900 border-b border-gray-200">
                                    <span>{{$plan->price}} {{$plan->currency}}</span><span class="ml-1 text-lg font-normal text-gray-500">/ {{$plan->invoice_interval}}</span>
                                </h1>
                                @foreach($plan->features as $feature)
                                <p class="flex items-center mb-2 text-gray-600">
                                    <span class="inline-flex items-center justify-center flex-shrink-0 w-4 h-4 mr-2 text-white bg-green-600 rounded-full">
                                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" class="w-3 h-3" viewBox="0 0 24 24">
                                            <path d="M20 6L9 17l-5-5"></path>
                                        </svg>
                                    </span>{{$feature->name}}
                                </p>
                                @endforeach
                                <form method="POST" action="{{ route('buy-plan') }}">
                                    @csrf
                                    <input type="hidden" name="plan" value="{{$plan->id}}">
                                    <button class="flex items-center w-full px-4 py-2 mt-auto text-white bg-indigo-400 border-0 rounded focus:outline-none hover:bg-indigo-500" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                        {{ __('Kup') }}
                                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-auto" viewBox="0 0 24 24">
                                            <path d="M5 12h14M12 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @can('activate quiz')
    <div class="pb-4">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-2 overflow-hidden bg-white border border-gray-200 sm:rounded-lg bg-gradient-to-r dark:bg-gray-900 dark:border-gray-700 sm:p-6">
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <div class="block pb-3 text-xl font-bold text-center text-white">Quizy do akceptacji:</div>
                </div>
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="hidden p-4">
                                <div class="flex items-center">
                                    <input id="checkbox-all-search" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="checkbox-all-search" class="sr-only">checkbox</label>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Title
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Questions
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inactive_quizzes as $quiz)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="hidden w-4 p-4">
                                <div class="flex items-center">
                                    <input id="checkbox-table-search-1" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                                </div>
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                {{$quiz->title}}
                            </th>
                            <td class="px-6 py-4">
                                {{$quiz->questions_count}}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{route('quizzes.show', $quiz->id)}}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Pokaż</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $inactive_quizzes->appends(['payoutsPage' => $payouts->currentPage()])->links() }}
            </div>
        </div>
    </div>
    @endcan
    @can('activate quiz')
    <div class="pb-4">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-2 overflow-hidden bg-white border border-gray-200 sm:rounded-lg bg-gradient-to-r dark:bg-gray-900 dark:border-gray-700 sm:p-6">
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <div class="block pb-3 text-xl font-bold text-center text-white">Ostanie wypłaty:</div>
                </div>
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="hidden p-4">
                                <div class="flex items-center">
                                    <input id="checkbox-all-search" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="checkbox-all-search" class="sr-only">checkbox</label>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Użytkownik
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Wartość
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payouts as $payout)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="hidden w-4 p-4">
                                <div class="flex items-center">
                                    <input id="checkbox-table-search-1" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                                </div>
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                {{$payout->user->name}}
                            </th>
                            <td class="px-6 py-4">
                                {{$payout->amount}}
                            </td>
                            <td class="px-6 py-4">
                                <x-payout-status-label :status="$payout->status" />
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{route('payout.show', $payout->id)}}" class="items-center w-full px-4 py-2 mt-auto text-white bg-indigo-400 border-0 rounded focus:outline-none hover:bg-indigo-500">Szczegóły</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $payouts->appends(['quizzesPage' => $inactive_quizzes->currentPage()])->links() }}
            </div>
        </div>
    </div>
    @endcan
</x-app-layout>
