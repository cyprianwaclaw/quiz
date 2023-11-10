<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class DifficultyFilter
{
    public function filter(Builder $builder, $value)
    {
        return $builder->where('difficulty', $value);
    }
}
