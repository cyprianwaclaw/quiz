<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Question
 *
 * @property int $id
 * @property int $quiz_id
 * @property string $question
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Answer[] $answers
 * @property-read int|null $answers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $answers_user
 * @property-read int|null $answers_user_count
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\Answer|null $correct_answer
 * @property-read \App\Models\Quiz $quiz
 * @method static \Database\Factories\QuestionFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereQuizId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Question extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'quiz_id', 'competition_id','correct_answer_id'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
    public function category()
    {
        return $this->hasOneThrough(Category::class, Quiz::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class,
            'question_id'
        );
    }

    public function correct_answer()
    {
        return $this->hasOne(Answer::class, 'id', 'correct_answer_id');
    }

    public function answers_user()
    {
        return $this->belongsToMany(
            User::class,
            AnswerUser::class,
            'question_id',
            'user_id'
        );

    }
}
