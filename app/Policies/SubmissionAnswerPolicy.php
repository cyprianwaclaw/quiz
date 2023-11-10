<?php

namespace App\Policies;

use App\Models\Answer;
use App\Models\SubmissionAnswer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubmissionAnswerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubmissionAnswer  $submissionAnswer
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, SubmissionAnswer $submissionAnswer)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubmissionAnswer  $submissionAnswer
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, SubmissionAnswer $submissionAnswer, Answer $answer)
    {
        if($submissionAnswer->whereRelation('answer', 'answer_id', '<>', '')->where('question_id',$answer->question_id)->count())
            return false;
        $quizSubmission = $submissionAnswer->quiz_submission;
        return ($quizSubmission->user_id === $user->id) && ($answer->question_id === $submissionAnswer->question_id);

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubmissionAnswer  $submissionAnswer
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, SubmissionAnswer $submissionAnswer)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubmissionAnswer  $submissionAnswer
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, SubmissionAnswer $submissionAnswer)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubmissionAnswer  $submissionAnswer
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, SubmissionAnswer $submissionAnswer)
    {
        //
    }
}
