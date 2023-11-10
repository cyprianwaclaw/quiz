<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\AnswerUser
 *
 * @property int $id
 * @property int $question_id
 * @property int $user_id
 * @property int $answer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Answer $answer
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\Quiz|null $quiz
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|AnswerUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnswerUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnswerUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnswerUser whereAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnswerUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnswerUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnswerUser whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnswerUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnswerUser whereUserId($value)
 * @mixin \Eloquent
 */
class AnswerUser extends Pivot
{

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->hasOneThrough(Category::class, Quiz::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
