<?php

namespace Modules\Filter\Filters\Strategies;

use Illuminate\Database\Eloquent\Builder;
use Modules\Filter\Filters\Filter;
use Modules\Filter\Filters\Resolve;
use Closure;

class With extends Filter
{
    /**
     * Operator string to detect in the query params.
     *
     * @var string
     */
    protected static string $operator = '$with';
    private $operators = ['$gt' => '>', '$gte' => '>=', '$lt' => '<', '$lte' => '<='];

    /**
     * Apply filter logic to $query.
     *
     * @return Closure
     */
    public function apply(): Closure
    {
        /** @var $query Builder */
        return function (Builder $query) {

            foreach ($this->values as $key => $value) {
                if (array_keys($value)[0] == 'count') {
                    foreach (array_keys($value['count']) as $array_key) {
                        $query->has($key, $this->operators[$array_key], $value['count'][$array_key]);
                    }
                }
                /*                $query->orWhere(function ($query) use ($value) {
                                    foreach ($value as $key => $item) {
                                        app(Resolve::class)->apply($query, $key, $item);
                                    }
                                });*/
            }
        };
    }
}
