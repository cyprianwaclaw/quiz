<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Competition;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use App\Events\AnsweredQuestion;
use App\Models\SubmissionAnswer;
use App\Models\CompetitionSubmission;
use App\Models\CompetitionSubmissionAnswer;
use App\Http\Requests\AnswerQuestionRequest;

class CompetitionSubmissionController extends Controller
{
    public function start(Competition $competition)
    {
        /** @var User $user */
        $user = auth()->user();
        // if(!$user->hasPremium()){
        //     return $this->sendError('Zasubskrybuj pakiet premium, aby rozwiązywać quizy');
        // }
        $competition_submission = new CompetitionSubmission();
        $competition_submission->competition_id = $competition->id;
        $competition_submission->user_id = $user->id;
        $competition_submission->save();
        $questions = $competition->questions()->inRandomOrder()->pluck('id');
        foreach ($questions as $question_id) {
            $submission_answer = new CompetitionSubmissionAnswer();
            $submission_answer->competition_submission_id = $competition_submission->id;
            $submission_answer->question_id = $question_id;
            $submission_answer->created_at = \Date::now();
            $submission_answer->save();
        }

        //    event(new QuizStarted($quiz, $user));
        // return $this->sendResponse([
        //     'submission_id' => $competition_submission->id,
        //     'competition_id' => $competition,
        //     // 'next_question' => $quiz_submission->getNextQuestion(),
        // ]);
        return response()->json([
            // 'success' => true,
            'submission_id'    => $competition_submission->id,
            'competition_info' => $competition,
            'next_question' => $competition_submission->getNextQuestion(),
        ], 200);
    }

    public function answer_question2(AnswerQuestionRequest $request, $competitionSubmissionId)
    {
        $user = User::find(auth()->id());
        $validated = $request->validated();
        $question = Question::findOrFail($validated['question_id']);
        $answer = Answer::findOrFail($validated['answer_id']);
        $competitionSubmission = CompetitionSubmission::findOrFail($competitionSubmissionId);

        // Znajdź lub utwórz odpowiedź w tabeli `competition_submission_answers`
        $submissionAnswer = CompetitionSubmissionAnswer::firstOrNew([
            'competition_submission_id' => $competitionSubmission->id,
            'question_id' => $question->id,
        ]);

        // Przypisz odpowiedź i zapisz
        $submissionAnswer->answer_id = $answer->id;
        $submissionAnswer->save();

        // Sprawdź, czy odpowiedź jest poprawna
        // $isCorrect = $answer->correct == 1;
        $isCorrect = $answer->correct == 1;

        // Aktualizuj liczbę poprawnych odpowiedzi w `competition_submissions`
        if ($isCorrect) {
            $competitionSubmission->increment('correct_answers'); // Zwiększa liczbę poprawnych odpowiedzi o 1
        }
        // Pobierz kolejne pytanie
        $nextQuestion = $competitionSubmission->getNextQuestion();

        return response()->json([
            // 'current_question' => new QuestionResource($question),
            'is_correct' => $isCorrect,
            'next_question' => $nextQuestion, // Kolejne pytanie lub `null`, jeśli to był ostatnie pytanie
        ], 200);
    }


    public function bestAnswers($competitionId)
    {
        // Pobieramy wszystkie submissiony związane z danym competition
        $competitionSubmissions = CompetitionSubmission::where('competition_id', $competitionId)->orderByDesc('correct_answers')->select('user_id', 'correct_answers' )->get();

        // Zbieramy dane użytkowników i liczymy poprawne odpowiedzi
        // $bestAnswers = [];

        // foreach ($competitionSubmissions as $submission) {
        //     $user = $submission->user; // Zakładając, że CompetitionSubmission ma relację z User
        //     // $correctAnswersCount = $submission->answers()->where('correct', 1)->count();

        //     // Dodajemy użytkownika do listy
        //     $bestAnswers[] = [
        //         'user_id' => $user->id,
        //         'username' => $user->name,
        //         'correct_answers_count' => 1,
        //         // 'correct_answers_count' => $correctAnswersCount,
        //     ];
        // }

        // Sortujemy odpowiedzi po liczbie poprawnych odpowiedzi (malejąco)
        // usort($bestAnswers, function ($a, $b) {
        //     return $b['correct_answers_count'] - $a['correct_answers_count'];
        // });

        // Zwracamy najlepsze odpowiedzi
        return response()->json([
            'best_answers' => $competitionSubmissions,
        ]);
    }


    public function answer_question(AnswerQuestionRequest $request, $competitionSubmission)
    {
        $user = User::find(auth()->id());
        $validated = $request->validated();
        $question = Question::findOrFail($validated['question_id']);
        $answer = Answer::findOrFail($validated['answer_id']);

        // Znajdź lub utwórz odpowiedź w tabeli `competition_submission_answers`
        $submissionAnswer = CompetitionSubmissionAnswer::firstOrNew([
            'competition_submission_id' => $competitionSubmission,
            'question_id' => $question->id,
            'answer_id' => $answer->id,

        ]);

        // Przypisz odpowiedź
        // $submissionAnswer->answer_id = $answer->id;
        // $submissionAnswer->save();

        // Upewnij się, że wartości są przypisane przed zapisem
        // $submissionAnswer->competition_submission_id = $competitionSubmission->id;
        // $submissionAnswer->question_id = $question->id;
        // $submissionAnswer->answer_id = $answer->id;
        $submissionAnswer->save();

        // Sprawdź, czy odpowiedź jest poprawna
        // $isCorrect = $answer->correct;

        // Zaktualizuj statystyki użytkownika
        // $userStats = $user->stats;
        // if ($isCorrect) {
        //     $userStats->increment('correct_answers');
        // } else {
        //     $userStats->increment('incorrect_answers');
        // }
        // $userStats->save();

        // Wyślij wydarzenie lub odpowiedź JSON
        // event(new AnsweredQuestion($user, $isCorrect));

        return response()->json([
            'data' => $question,
            // 'submission_answer' => $submissionAnswer,
            'submission' => $competitionSubmission,
            'data' => $submissionAnswer,
            'isCorrect_answer' => $answer->correct == 1 ? true : false,
            // 'next_question' => $competitionSubmission->getNextQuestion(),

        ], 200);
    }

    public function answer_question1(AnswerQuestionRequest $request, CompetitionSubmission $competitionSubmission)
    {
        $user = User::find(auth()->id());
        $validated = $request->validated();
        $question = Question::findOrFail($validated['question_id']);
        $submissionAnswer = CompetitionSubmissionAnswer::firstWhere([
            ['question_id', '=', $question->id],
            // ['quiz_submission_id', '=', $competitionSubmission->id],
            ['id', '=', $competitionSubmission->id],
        ]);

        // if (!$submissionAnswer) {
        //     return response()->json([
        //         'error' => 'Submission answer not found',
        //     ], 404);

        $answer = Answer::findOrFail($validated['answer_id']);
        // !walidacja premium plan
        // if ($user->cannot('update', [$submissionAnswer, $answer])) {
        // return $this->sendError( 'Unauthorized','You cannot do this',401);
        // }
        // $submissionAnswer->answer()->associate($answer)->save();
        // if ($submissionAnswer->answer()->associate($answer)->save()) {

        $isCorrect = $answer->correct;

        // Update user stats
        // $user = User::find(auth()->id());
        $userStats = $user->stats;
        // $gameCorrectQuestion = 0;
        if ($isCorrect) {
            // $this->gameCorrectQuestion += 1;
            $userStats->increment('correct_answers');
        } else {
            $userStats->increment('incorrect_answers');
        }

        $userStats->save();

        // event(new AnsweredQuestion($user, $isCorrect));

        // $response = [
        //     'submission_id' => $competitionSubmission->id,
        //     'is_correct' => $isCorrect,
        //     'next_question' => $this->getNextQuestion($competitionSubmission),
        // ];
        return response()->json([
            // 'success' => true,
            'data'    => $question,
            'message' => $submissionAnswer,
            'answer' => $answer,
            // 'next_question' => $competition_submission->getNextQuestion(),
        ], 200);
        // questions_count
        // $quizId = QuizSubmission::find($quizSubmission->id)->quiz_id;
        // if (is_null($response['next_question'])) {
        //     $quizDuration = gmdate('i:s', $quizSubmission->created_at->diffInSeconds($quizSubmission->ended_at));
        //     $response['quiz_time'] = $quizDuration;
        //     // $response['quiz_id'] = $quizId;
        //     $response['game_correct_question'] = $this->gameCorrectQuestion;
        //     $response['quiz_questions_count'] = Quiz::find($quizId)->questions_count;
        //     $response['quiz_id_data'] = Quiz::find($quizId)->title;
        // }

        // return $this->sendResponse($response);
        // }
    }
}
