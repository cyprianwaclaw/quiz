@props([
'status'
])

@php
    $classes = 'text-sm font-medium mr-2 px-2.5 py-0.5 rounded ';
    $txt = "";
    switch ($status){
        case 'in_progress':
            $classes .= 'bg-yellow-100 text-yellow-800 dark:bg-yellow-200 dark:text-yellow-900';
            $txt = "W oczekiwaniu na przelew";
            break;
        case 'success':
            $classes .= 'bg-green-100 text-green-800 dark:bg-green-200 dark:text-green-900';
            $txt = "Zrealizowano pomyślnie";
            break;
        case 'fail':
            $classes .= 'bg-red-100 text-red-800 dark:bg-red-200 dark:text-red-900';
            $txt = "Błąd wypłaty";
            break;
    }
@endphp
<span {{ $attributes->merge(['class' => $classes]) }}>{{$txt}}</span>
