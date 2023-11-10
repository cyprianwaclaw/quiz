<?php

namespace Modules\Filter\Filters\Strategies;

use Modules\Filter\Filters\Filter;
use Closure;

class StartsWithCaseSensitiveFilter extends Filter
{
    /**
     * Operator string to detect in the query params.
     *
     * @var string
     */
    protected static string $operator = '$startsWithc';

    /**
     * Apply filter logic to $query.
     *
     * @return Closure
     */
    public function apply(): Closure
    {
        return function ($query) {
            foreach ($this->values as $value) {
                $query->whereRaw("BINARY `{$this->column}` like ?", $value.'%');
            }
        };
    }
}
