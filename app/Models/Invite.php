<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Models\Invite
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\InviteFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Invite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invite query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invite whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invite whereUserId($value)
 * @mixin \Eloquent
 */
// class Invite extends Model
// {
//     use HasFactory;

//     protected $fillable = [
//         'user_id', 'token'
//     ];

//     public function user()
//     {
//         return $this->belongsTo(User::class);
//     }
// }
class Invite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'token'
    ];

    protected static function boot()
    {
        parent::boot();

        // Dodaj automatyczne generowanie tokena przed utworzeniem nowego rekordu
        static::creating(function ($invite) {
            $invite->token = Str::random(20); // Generowanie losowego tokena o długości 20 znaków
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
// <?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// /**
//  * App\Models\Invite
//  *
//  * @property int $id
//  * @property int $user_id
//  * @property string $token
//  * @property \Illuminate\Support\Carbon|null $created_at
//  * @property \Illuminate\Support\Carbon|null $updated_at
//  * @property-read \App\Models\User $user
//  * @method static \Database\Factories\InviteFactory factory(...$parameters)
//  * @method static \Illuminate\Database\Eloquent\Builder|Invite newModelQuery()
//  * @method static \Illuminate\Database\Eloquent\Builder|Invite newQuery()
//  * @method static \Illuminate\Database\Eloquent\Builder|Invite query()
//  * @method static \Illuminate\Database\Eloquent\Builder|Invite whereCreatedAt($value)
//  * @method static \Illuminate\Database\Eloquent\Builder|Invite whereId($value)
//  * @method static \Illuminate\Database\Eloquent\Builder|Invite whereToken($value)
//  * @method static \Illuminate\Database\Eloquent\Builder|Invite whereUpdatedAt($value)
//  * @method static \Illuminate\Database\Eloquent\Builder|Invite whereUserId($value)
//  * @mixin \Eloquent
//  */
// class Invite extends Model
// {
//     use HasFactory;

//     protected $fillable = [
//         'user_id', 'token'
//     ];

//     public function user()
//     {
//         return $this->belongsTo(User::class);
//     }
// }