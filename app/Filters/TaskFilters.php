// app/Filters/TaskFilter.php

<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class TaskFilter
{
    public static function apply(Builder $query, $filters)
    {
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['completed'])) {
            $query->where('completed', $filters['completed']);
        }
    }
}
