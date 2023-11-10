<?php

namespace App\Enums;

class PayoutStatus
{
    const IN_PROGRESS = 'in_progress';
    const SUCCESS = 'success';
    const FAIL = 'fail';

    const TYPES = [
        self::IN_PROGRESS,
        self::SUCCESS,
        self::FAIL,
    ];

    const TYYPES_WITH_TEXT = [
        self::IN_PROGRESS => 'W oczekiwaniu na przelew',
        self::SUCCESS => 'Zrealizowano pomyślnie',
        self::FAIL => 'Błąd wypłaty',
    ];
}
