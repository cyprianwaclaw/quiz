<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionSubmissionAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['competition_submission_id', 'question_id', 'answer_id'];


    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // public function quiz_submission()
    // {
    //     return $this->belongsTo(QuizSubmission::class);
    // }
    public function competition_submission()
    {
        return $this->belongsTo(CompetitionSubmission::class);
    }
}
