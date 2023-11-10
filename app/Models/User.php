<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Rinvex\Subscriptions\Traits\HasPlanSubscriptions;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User
 *
 * @property string $name
 * @property string $surname
 * @property string $phone
 * @property string $email
 * @property string $password
 * @property int $invited_by
 * @property int $points
 * @property string $avatar_path
 * @property Company $company
 * @property Financial $financial
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Address|null $address
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Question[] $answeredQuestions
 * @property-read int|null $answered_questions_count
 * @property-read \App\Models\Invite|null $invite
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $invited
 * @property-read int|null $invited_count
 * @property-read User|null $inviting
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PlanSubscription[] $planSubscriptions
 * @property-read int|null $plan_subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\UserStats|null $stats
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatarPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereInvitedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
        'phone'
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
            ->belongsToMany(Question::class,AnswerUser::class)
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
        return Question::whereDoesntHave('answers_user', function($q) use ($id) {
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

    public function payments()
    {
        return $this->hasManyThrough(Payment::class,PlanSubscription::class, 'subscriber_id');
    }


}
