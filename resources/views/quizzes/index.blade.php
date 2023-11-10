<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <x-alert/>

    @can('activate quiz')
    <div class="pb-4 mt-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="sm:rounded-lg bg-gradient-to-r bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-2 sm:p-6 overflow-hidden">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <div class="block text-white text-xl font-bold text-center pb-3">Wszystkie quizy:</div>
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
                                Title
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Questions
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Is active
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($quizzes as $quiz)
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
                            <td class="px-6 py-4">
                                {{$quiz->questions_count}}
                            </td>
                            <td class="px-6 py-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 {{$quiz->is_active?"stroke-green-500":"stroke-red-500"}}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{route('quizzes.show', $quiz->id)}}"
                                   class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Pokaż</a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $quizzes->appends(['payoutsPage' => $quizzes->currentPage()])->links() }}
                </div>
        </div>
    </div>
    @endcan
</x-app-layout>
