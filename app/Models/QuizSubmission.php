<?php

namespace App\Models;

use App\Http\Resources\API\QuestionResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * QuizSubmission Controller
 *
 * @property int $id
 * @property int $quiz_id
 * @property int $user_id
 * @property int $started_at
 * @property int $ended_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Quiz $quiz
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SubmissionAnswer[] $submission_answers
 * @property-read int|null $submission_answers_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\QuizSubmissionFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|QuizSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuizSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuizSubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuizSubmission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuizSubmission whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuizSubmission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuizSubmission whereQuizId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuizSubmission whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuizSubmission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuizSubmission whereUserId($value)
 * @mixin \Eloquent
 */

class QuizSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'started_at',
        'ended_at',
    ];

    protected static function booted()
    {
        static::deleting(function ($quizSubmission){
            $quizSubmission->submission_answers->each->delete();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * @return HasMany
     */
    public function questions()
    {
        return $this->quiz->questions();
    }

    public function submission_answers()
    {
        return $this->hasMany(SubmissionAnswer::class);
    }

    public function getAnsweredQuestions()
    {
        return $this->submission_answers()->whereNotNull('answer_id');
    }

    public function getUnansweredQuestions()
    {
        return $this->submission_answers()->whereNull('answer_id');
    }

    /**
     * Get next unasnwered question
     * @return QuestionResource|null
     */
    public function getNextQuestion(): ?QuestionResource
    {
        $unansweredQuestions = $this->getUnansweredQuestions();
        if($unansweredQuestions->count()) {
            $question = Question::findOrFail($this->getUnansweredQuestions()->first()->question_id);
            return new QuestionResource($question);
        }else {
            $now = now();
            $this->ended_at = now();
            $this->save();
            $quizTime = $now->diffInSeconds($this->created_at);
            return null;
        }
    }
}
