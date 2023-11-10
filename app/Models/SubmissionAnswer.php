<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\SubmissionAnswer
 *
 * @property int $id
 * @property int $quiz_submission_id
 * @property int $question_id
 * @property int $answer_id
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Answer|null $answer
 * @property-read \App\Models\QuizSubmission $quiz_submission
 * @method static \Database\Factories\SubmissionAnswerFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|SubmissionAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubmissionAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubmissionAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubmissionAnswer whereAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubmissionAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubmissionAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubmissionAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubmissionAnswer whereQuizSubmissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubmissionAnswer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SubmissionAnswer extends Model
{
    use HasFactory;

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function quiz_submission()
    {
        return $this->belongsTo(QuizSubmission::class);
    }
}
