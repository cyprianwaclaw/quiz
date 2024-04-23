<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserStats
 *
 * @property int $correct_answers
 * @property int $incorrect_answers
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\UserStatsFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|UserStats newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserStats newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserStats query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserStats whereCorrectAnswers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserStats whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserStats whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserStats whereIncorrectAnswers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserStats whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserStats whereUserId($value)
 * @mixin \Eloquent
 */
class UserStats extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'correct_answers',
        'incorrect_answers',
        'user_id', // Dodajemy user_id do fillable
    ];
}
