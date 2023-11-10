<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class SearchFilter
{
    protected array $searchableFields = ['title', 'description'];

    public function filter(Builder $builder, $value)
    {
        $builder->where(function ($builder) use ($value) {
            $searchWildcard = '%' . $value . '%';
            foreach ($this->searchableFields as $field) {
                $builder->orWhere($field, 'LIKE', $searchWildcard);
            }
        });
        return $builder;
    }
}
