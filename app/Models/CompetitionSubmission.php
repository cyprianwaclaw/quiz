<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\API\QuestionResource;

class CompetitionSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        // 'quiz_id',
        'user_id',
        'started_at',
        'ended_at',
    ];
    // public function answers()
    // {
    //     return $this->hasMany(CompetitionSubmissionAnswer::class);
    // }
    public function answers()
    {
        return $this->hasMany(CompetitionSubmissionAnswer::class, 'competition_submission_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
    public function questions()
    {
        return $this->competition->questions();
    }
    public function competition_submission_answers()
    {
        return $this->hasMany(CompetitionSubmissionAnswer::class);
    }

    public function getAnsweredQuestions()
    {
        return $this->competition_submission_answers()->whereNotNull('answer_id');
    }

    public function getUnansweredQuestions()
    {
        return $this->competition_submission_answers()->whereNull('answer_id');
    }

    /**
     * Get next unasnwered question
     * @return QuestionResource|null
     */
    public function getNextQuestion(): ?QuestionResource
    {
        $unansweredQuestions = $this->getUnansweredQuestions();
        if ($unansweredQuestions->count()) {
            $question = Question::findOrFail($this->getUnansweredQuestions()->first()->question_id);
            return new QuestionResource($question);
        } else {
            $now = now();
            $this->ended_at = now();
            $this->save();
            $quizTime = $now->diffInSeconds($this->created_at);
            return null;
        }
    }
}
