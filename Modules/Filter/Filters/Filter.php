<?php

namespace Modules\Filter\Filters;

use Illuminate\Database\Eloquent\Builder;

abstract class Filter implements \Modules\Filter\Contracts\Filter
{
    protected Builder $query;
    protected string  $column;
    protected array   $values;

    public function __construct(Builder $query, string $column, array $values)
    {
        $this->query = $query;
        $this->column = $column;
        $this->values = $values;
    }

    /**
     * @return string
     */
    public static function operator(): string
    {
        return static::$operator;
    }
}
