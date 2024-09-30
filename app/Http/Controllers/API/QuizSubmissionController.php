<?php

namespace App\Http\Controllers\API;

use App\Events\AnsweredQuestion;
use App\Events\QuizStarted;
use App\Http\Requests\AnswerQuestionRequest;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\SubmissionAnswer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class QuizSubmissionController extends APIController
{
    /**
     * Start quiz
     * @group Quiz
     *
     * @urlParam quiz integer required The ID of the Quiz. Example: 2
     * @responseFile 200 scenario="Success" storage/api-docs/responses/quiz/start.200.json
     * @responseFile 404 scenario="Not found" storage/api-docs/responses/resource.404.json
     * @param Quiz $quiz
     * @return JsonResponse|void
     */
    public function start(Quiz $quiz)
    {
        /** @var User $user */
        $user = auth()->user();
        // if(!$user->hasPremium()){
        //     return $this->sendError('Zasubskrybuj pakiet premium, aby rozwiązywać quizy');
        // }
        $quiz_submission = new QuizSubmission();
        $quiz_submission->quiz_id = $quiz->id;
        $quiz_submission->user_id = $user->id;
        $quiz_submission->save();
        $questions = $quiz->questions()->inRandomOrder()->pluck('id');
        foreach ($questions as $question_id) {
            $submission_answer = new SubmissionAnswer();
            $submission_answer->quiz_submission_id = $quiz_submission->id;
            $submission_answer->question_id = $question_id;
            $submission_answer->created_at = \Date::now();
            $submission_answer->save();
        }

//        event(new QuizStarted($quiz, $user));
        return $this->sendResponse([
            'submission_id' => $quiz_submission->id,
            'next_question' => $quiz_submission->getNextQuestion(),
        ]);

    }

    /**
     * Answer question
     * @group Quiz
     *
     * @urlParam quiz_submission integer required The ID of the Quiz Submission. Example: 2
     * @bodyParam question_id integer required The ID of the Question. Example: 15
     * @bodyParam answer_id integer required The ID of the Answer. Example: 1
     * @responseFile 200 scenario="Success" storage/api-docs/responses/quiz/answer_question.200.json
     * @responseFile 200 scenario="Success, Last question answered" storage/api-docs/responses/quiz/answer_question_last.200.json
     * @responseFile 401 scenario="Unathorized" storage/api-docs/responses/quiz/answer_question.401.json
     * @param AnswerQuestionRequest $request
     * @param QuizSubmission $quizSubmission
     * @return JsonResponse|void
     */
    public function answer_question(AnswerQuestionRequest $request, QuizSubmission $quizSubmission)
    {
        $user = User::find(auth()->id());
        $validated = $request->validated();
        $question = Question::findOrFail($validated['question_id']);
        $submissionAnswer = SubmissionAnswer::firstWhere([
            ['question_id', '=', $question->id],
            ['quiz_submission_id', '=', $quizSubmission->id],
        ]);
        $answer = Answer::findOrFail($validated['answer_id']);
        // !walidacja premium plan
        // if ($user->cannot('update', [$submissionAnswer, $answer])) {
            // return $this->sendError( 'Unauthorized','You cannot do this',401);
        // }
        // if($submissionAnswer->answer()->associate($answer)->save()){
        //     event(new AnsweredQuestion($user, $answer->correct));
        //     $response = [
        //         'submission_id' => $quizSubmission->id,
        //         'is_correct' => $answer->correct,
        //         'next_question' => $this->getNextQuestion($quizSubmission),
        //     ];
        //     if (is_null($response['next_question'])) {
        //         $quizDuration = gmdate('i:s', $quizSubmission->created_at->diffInSeconds($quizSubmission->ended_at));
        //         $response['quiz_time'] = $quizDuration;
        //     }
        if ($submissionAnswer->answer()->associate($answer)->save()) {
            $isCorrect = $answer->correct;

            // Update user stats
            $user = User::find(auth()->id());
            $userStats = $user->stats;

            if ($isCorrect) {
                $userStats->increment('correct_answers');
            } else {
                $userStats->increment('incorrect_answers');
            }

            $userStats->save();

            event(new AnsweredQuestion($user, $isCorrect));

            $response = [
                'submission_id' => $quizSubmission->id,
                'is_correct' => $isCorrect,
                'next_question' => $this->getNextQuestion($quizSubmission),
            ];

            if (is_null($response['next_question'])) {
                $quizDuration = gmdate('i:s', $quizSubmission->created_at->diffInSeconds($quizSubmission->ended_at));
                $response['quiz_time'] = $quizDuration;
            }

            return $this->sendResponse($response);
        }
    }

    /**
     * Get next question
     * @group Quiz
     *
     * @urlParam quiz_submission integer required The ID of the Quiz Submission. Example: 2
     * @responseFile 200 scenario="Success" storage/api-docs/responses/quiz/get_next_question.200.json
     * @responseFile 401 scenario="Unathorized" storage/api-docs/responses/quiz/answer_question.401.json
     * @param QuizSubmission $quizSubmission
     * @return JsonResponse|void
     */
    public function getNextQuestion(QuizSubmission $quizSubmission)
    {
        return $quizSubmission->getNextQuestion();
    }

}
