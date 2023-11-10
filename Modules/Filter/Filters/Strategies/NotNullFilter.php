<?php

namespace Modules\Filter\Filters\Strategies;

use Modules\Filter\Filters\Filter;
use Closure;

class NotNullFilter extends Filter
{
    /**
     * Operator string to detect in the query params.
     *
     * @var string
     */
    protected static string $operator = '$notNull';

    /**
     * Apply filter logic to $query.
     *
     * @return Closure
     */
    public function apply(): Closure
    {
        return function ($query) {
            $query->whereNotNull($this->column);
        };
    }
}
