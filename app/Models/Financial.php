<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Financial
 *
 * @property string $iban
 * @property string $bank_name
 * @property string $swift
 * @property int $id
 * @property int $user_id
 * @property string|null $card_number
 * @property string|null $card_expiration_date
 * @property string|null $card_cvc
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\FinancialFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Financial newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Financial newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Financial query()
 * @method static \Illuminate\Database\Eloquent\Builder|Financial whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Financial whereCardCvc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Financial whereCardExpirationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Financial whereCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Financial whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Financial whereIban($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Financial whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Financial whereSwift($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Financial whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Financial whereUserId($value)
 * @mixin \Eloquent
 */
class Financial extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_name',
        'iban',
        'swift',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
