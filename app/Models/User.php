<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Rinvex\Subscriptions\Traits\HasPlanSubscriptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPlanSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'invited_by',
        'points',
        'avatar_path',
        'phone',
        'verification_code',
        'email_verified_at',
        'email_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
    public function competitions()
    {
        return $this->hasMany(Quiz::class);
    }

    public function financial()
    {
        return $this->hasOne(Financial::class);
    }

    public function invite()
    {
        return $this->hasOne(Invite::class);
    }

    public function invited()   // zaproszeni uzytkownicy
    {
        return $this->hasMany(User::class, 'invited_by', 'id');
    }

    public function inviting()  // zaproszony przez
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function stats()     // statystyki
    {
        return $this->hasOne(UserStats::class);
    }

    // testowanie domslnie przypisanie statystyk
    protected static function booted()
    {
        // parent::boote();

        static::created(function ($user) {
            $user->createUserStats();
        });
    }

    public function createUserStats()
    {
        UserStats::create([
            'user_id' => $this->id,
            'correct_answers' => 0,
            'incorrect_answers' => 0,
        ]);
    }

    public function addPoints($value)
    {
        $this->points += $value;
        $this->save();
    }

    public function subtractPoints($value)
    {
        $this->points -= $value;
        $this->save();
    }

    public function answeredQuestions()
    {
        return $this->belongsToMany(
            Question::class,
            AnswerUser::class
        );
    }

    public function answeredByCategory(Category $category)
    {
        return $this
            ->belongsToMany(Question::class, AnswerUser::class)
            ->whereRelation('category', 'category_id', '=', $category->id);
    }

    public function answeredQuestionsByCategory($category_id)
    {
        return $this->belongsToMany(
            Question::class,
            AnswerUser::class
        )->where('category_id', $category_id)->withPivot(['answer_id']);
    }

    public function unansweredQuestions()
    {
        $id = $this->id;
        return Question::whereDoesntHave('answers_user', function ($q) use ($id) {
            $q->where('user_id', $id);
        });
    }

    public function hasPremium(): bool
    {
        return (bool)($this->activePlanSubscriptions())->count();
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class);
    }

    public function planSubscriptions()
    {
        return $this->hasMany(PlanSubscription::class, 'subscriber_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    // public function planSubscriptions()
    // {
    //     return $this->morphMany(PlanSubscription::class, 'subscriber');
    // }

    // public function payments()
    // {
    //     return $this->hasManyThrough(
    //         Payment::class,
    //         PlanSubscription::class,
    //         'subscriber_id', // to nie zadziała, bo masz morphs!
    //         'plan_subscription_id',
    //         'id',
    //         'id'
    //     );
    // }

}
