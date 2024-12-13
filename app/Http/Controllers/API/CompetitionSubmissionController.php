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

    public function answer_question(AnswerQuestionRequest $request, $competitionSubmissionId)
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
        // $competitionDuration = gmdate('i:s', $submissionAnswer->created_at->diffInSeconds($submissionAnswer->ended_at));
        $competitionDuration = $submissionAnswer->created_at->diffInSeconds($submissionAnswer->ended_at);
        if ($nextQuestion == null) {

            $competitionSubmission->duration = $competitionDuration;
            $competitionSubmission->save();
        }
        return response()->json([
            // 'current_question' => new QuestionResource($question),
            'is_correct' => $isCorrect,
            'next_question' => $nextQuestion,
            'duration' => $competitionDuration ? $competitionDuration : null,
        ], 200);
    }


    // public function bestAnswers($competitionId)
    // {
    //     $competitionSubmissions = CompetitionSubmission::where('competition_id', $competitionId)
    //         ->orderByDesc('correct_answers') // Sortowanie po poprawnych odpowiedziach (malejąco)
    //         ->orderBy('duration')           // Sortowanie po czasie (rosnąco)
    //         ->select('id', 'user_id', 'correct_answers', 'duration')
    //         ->get()
    //         ->map(function ($submission, $index) {
    //             $submission->place = $index + 1; // Dodaj pole `place` z miejscem (indeks + 1)
    //             return $submission;
    //         });

    //     // Aktualizuj punkty użytkowników na podstawie miejsca
    //     foreach ($competitionSubmissions as $submission) {
    //         $user = User::find($submission->user_id);

    //         if ($user) {
    //             // Przyznawanie punktów na podstawie miejsca
    //             switch ($submission->place) {
    //                 case 1:
    //                     $user->points += 100; // Punkty za 1. miejsce
    //                     break;
    //                 case 2:
    //                     $user->points += 50; // Punkty za 2. miejsce
    //                     break;
    //                 case 3:
    //                     $user->points += 25; // Punkty za 3. miejsce
    //                     break;
    //             }

    //             // Zapisz zaktualizowane punkty użytkownika
    //             $user->save();
    //         }

    //         // Zaktualizuj miejsce w tabeli `competition_submissions`
    //         $competitionSubmission = CompetitionSubmission::find($submission->id);
    //         if ($competitionSubmission) {
    //             $competitionSubmission->place = $submission->place;
    //             $competitionSubmission->save();
    //         }
    //     }

    //     // Zwracamy najlepsze odpowiedzi z miejscami
    //     // return response()->json([
    //     //     'best_answers' => $competitionSubmissions,
    //     // ]);
    // }
}
