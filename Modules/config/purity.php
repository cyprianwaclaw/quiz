<?php
use Modules\Filter\Filters\Strategies\AndFilter;
use Modules\Filter\Filters\Strategies\BetweenFilter;
use Modules\Filter\Filters\Strategies\ContainsCaseSensitiveFilter;
use Modules\Filter\Filters\Strategies\ContainsFilter;
use Modules\Filter\Filters\Strategies\EndsWithCaseSensitiveFilter;
use Modules\Filter\Filters\Strategies\EndsWithFilter;
use Modules\Filter\Filters\Strategies\EqualCaseSensitiveFilter;
use Modules\Filter\Filters\Strategies\EqualFilter;
use Modules\Filter\Filters\Strategies\GreaterOrEqualFilter;
use Modules\Filter\Filters\Strategies\GreaterThanFilter;
use Modules\Filter\Filters\Strategies\InFilter;
use Modules\Filter\Filters\Strategies\LessOrEqualFilter;
use Modules\Filter\Filters\Strategies\LessThanFilter;
use Modules\Filter\Filters\Strategies\NotContainsFilter;
use Modules\Filter\Filters\Strategies\NotContainsSensitiveFilter;
use Modules\Filter\Filters\Strategies\NotEqualFilter;
use Modules\Filter\Filters\Strategies\NotInFilter;
use Modules\Filter\Filters\Strategies\NotNullFilter;
use Modules\Filter\Filters\Strategies\NullFilter;
use Modules\Filter\Filters\Strategies\OrFilter;
use Modules\Filter\Filters\Strategies\StartsWithCaseSensitiveFilter;
use Modules\Filter\Filters\Strategies\StartsWithFilter;

return [
    'filters' => [
        EqualFilter::class,
        InFilter::class,
        BetweenFilter::class,
        ContainsFilter::class,
        EndsWithFilter::class,
        GreaterThanFilter::class,
        NotNullFilter::class,
        StartsWithFilter::class,
        GreaterOrEqualFilter::class,
        LessOrEqualFilter::class,
        LessThanFilter::class,
        NotContainsFilter::class,
        NotEqualFilter::class,
        NotInFilter::class,
        NullFilter::class,
        AndFilter::class,
        OrFilter::class,
        NotContainsSensitiveFilter::class,
        StartsWithCaseSensitiveFilter::class,
        EndsWithCaseSensitiveFilter::class,
        EqualCaseSensitiveFilter::class,
        ContainsCaseSensitiveFilter::class,
        \Modules\Filter\Filters\Strategies\With::class,
    ],

    'silent' => false,

//    'custom_filters_location' => app_path('Filters'),
    'custom_filters_location' => '',
];
