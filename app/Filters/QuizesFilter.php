<?php

namespace App\Filters;

class QuizesFilter
{
    public static function apply($query, $filters)
    {
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // if (isset($filters['selected'])) {
        //     $selected = $filters['selected'] === 'true' ? true : false;
        //     $query->where('selected', $selected);
        // }

        if (isset($filters['order'])) {
            $order = $filters['order'] == 'desc' ? 'desc' : 'asc';
            $query->orderBy('created_at', $order);
        }

        // if (isset($filters['userIdFrom']) && isset($filters['userIdTo'])) {
        //     self::filterByUserIdRange($query, $filters['userIdFrom'], $filters['userIdTo']);
        // }

        // Dodaj dodatkowe ograniczenie dla kategorii, jeśli została przekazana
        if (isset($filters['category_titles'])) {
            self::filterByCategoryTitles($query, $filters['category_titles']);
        }
    }

    public static function filterByUserIdRange($query, $userIdFrom, $userIdTo)
    {
        $query->whereBetween('user_id', [$userIdFrom, $userIdTo]);
    }

    public static function filterByCategoryTitles($query, $categoryTitles)
    {
        // Dodaj filtr tylko wtedy, gdy przekazano tytuły kategorii
        if (!empty($categoryTitles)) {
            $query->whereHas('categories', function ($categoryQuery) use ($categoryTitles) {
                $categoryQuery->whereIn('title', $categoryTitles);
            });
        }
    }

    public static function paginate($query, $perPage, $page)
    {
        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}



// Kod w klasie TaskFilters jest odpowiedzialny za aplikowanie różnych filtrów na
// zapytanie Eloquent i paginowanie wyników. Oto szczegółowe wyjaśnienie każdej z metod:
// apply($query, $filters):
// Ta metoda przyjmuje dwa argumenty: zapytanie Eloquent ($query) i tablicę
// filtrów ($filters), które definiują kryteria, na podstawie których będziesz
// filtrować wyniki zadań. Jeśli istnieje klucz 'user_id' w tablicy $filters,
// to jest to kryterium, które oznacza, że chcesz wyniki ograniczyć do zadań
// przypisanych do określonego użytkownika. W takim przypadku jest używana metoda
// where do dodania warunku, który sprawi, że tylko zadania przypisane do określonego
// użytkownika zostaną uwzględnione w wynikach. Jeśli istnieje klucz 'selected',
// to jest to kryterium dotyczące wyboru zadania (np. 'true' lub 'false'). Jeśli jest
// 'true', to zadania oznaczone jako wybrane zostaną uwzględnione w wynikach (warunek where).
//  Jeśli jest 'false', to zadania niewybrane zostaną uwzględnione. Jeśli istnieje
//  klucz 'order', to jest to kryterium dotyczące kolejności sortowania wyników. Jeśli
//  jest 'desc', wyniki zostaną posortowane malejąco, jeśli 'asc', wyniki zostaną
//  posortowane rosnąco. Jeśli istnieją zarówno klucze 'userIdFrom' i 'userIdTo', to
//  jest to kryterium zakresu użytkowników. Wywoływana jest metoda filterByUserIdRange,
//  aby uwzględnić w wynikach tylko zadania przypisane do użytkowników w określonym
//  zakresie identyfikatorów. filterByUserIdRange($query, $userIdFrom, $userIdTo)
//  Ta metoda dodaje do zapytania Eloquent warunek, który uwzględnia zadania przypisane do
//  użytkowników o identyfikatorach z zakresu od $userIdFrom do $userIdTo. W praktyce
//  to ogranicza wyniki do zadań przypisanych do użytkowników o identyfikatorach z określonego zakresu.
// paginate($query, $perPage, $page) Ta metoda służy do paginacji wyników. Przyjmuje
// zapytanie Eloquent ($query), liczbę wyników na stronę ($perPage) i numer
// strony ($page). Wywołuje metodę paginate na zapytaniu,
// co powoduje podzielenie
// wyników na strony i zwrócenie wyników dla danej strony.
