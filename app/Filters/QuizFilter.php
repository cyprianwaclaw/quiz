<?php

namespace App\Filters;

class QuizFilter extends AbstractFilter
{
    protected array $filters = [
        'difficulty' => DifficultyFilter::class,
        'time' => TimeFilter::class,
        'sort' => SortFilter::class,
        'search' => SearchFilter::class,
    ];
}
