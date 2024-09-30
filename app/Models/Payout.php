<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *  App\Models\Payout
 * @mixin \Eloquent
 */
class Payout extends Model
{
    use HasFactory;

    protected $casts = [
        'user_id' => 'integer',
        'points' => 'integer',
        'amount' => 'float'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}