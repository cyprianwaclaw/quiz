<?php

// namespace App\Enums;

// class PayoutStatus
// {
//     const IN_PROGRESS = 'in_progress';
//     const SUCCESS = 'success';
//     const FAIL = 'fail';

//     const TYPES = [
//         self::IN_PROGRESS,
//         self::SUCCESS,
//         self::FAIL,
//     ];

//     const TYYPES_WITH_TEXT = [
//         self::IN_PROGRESS => 'W oczekiwaniu na przelew',
//         self::SUCCESS => 'Zrealizowano pomyślnie',
//         self::FAIL => 'Błąd wypłaty',
//     ];
//     public static function getText(string $status): string
//     {
//         return self::TYPES_WITH_TEXT[$status] ?? 'Nieznany status';
//     }
// }

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

    const TYPES_WITH_TEXT = [
        self::IN_PROGRESS => 'W oczekiwaniu na przelew',
        self::SUCCESS => 'Zrealizowano',
        self::FAIL => 'Błąd wypłaty',
    ];

    const TYPES_WITH_TEXT_PAYMENTS = [
        self::IN_PROGRESS => 'Oczekująca',
        self::SUCCESS => 'Zrealizowano',
        self::FAIL => 'Błąd płatności',
    ];

    public static function getText(string $status): string
    {
        return self::TYPES_WITH_TEXT_PAYMENTS[$status] ?? 'Nieznany status';
    }
}