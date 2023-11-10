<?php

namespace Modules\Filter\Filters\Strategies;

use Modules\Filter\Filters\Filter;
use Closure;

class NullFilter extends Filter
{
    /**
     * Operator string to detect in the query params.
     *
     * @var string
     */
    protected static string $operator = '$null';

    /**
     * Apply filter logic to $query.
     *
     * @return Closure
     */
    public function apply(): Closure
    {
        return function ($query) {
            $query->whereNull($this->column);
        };
    }
}
