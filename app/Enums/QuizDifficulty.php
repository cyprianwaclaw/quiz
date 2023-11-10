<?php

namespace App\Enums;

class QuizDifficulty
{
    const EASY = 'easy';
    const MEDIUM = 'medium';
    const HARD = 'hard';

    const TYPES = [
        self::EASY,
        self::MEDIUM,
        self::HARD,
    ];
}
