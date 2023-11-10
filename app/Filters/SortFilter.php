<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class SortFilter
{
    public function filter(Builder $builder, $value)
    {
        foreach ($this->normalizeValues($value) as $field => $order) {
            $builder->orderBy($field, $order);
        }
        return $builder;
    }

    private function normalizeValues($values)
    {
        $normalized = [];

        foreach ((array)$values as $value) {

            $exploded = explode(',', $value);

            if (!empty($exploded[1]) and in_array($exploded[1], ['asc', 'desc'])) {
                $normalized[$exploded[0]] = $exploded[1];
                continue;
            }

            $normalized[$exploded[0]] = 'asc';
        }

        return $normalized;
    }
}
