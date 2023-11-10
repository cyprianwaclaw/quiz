<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class TimeFilter
{
    public function filter(Builder $builder, $value)
    {
        return $builder->where('time', $value);
    }
}
