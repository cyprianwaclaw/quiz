<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;
use App\Models\Question;
use App\Models\Competition;
use App\Http\Requests\StoreCompetitionRequest;
use App\Models\CompetitionSubmission;
use Illuminate\Support\Facades\Storage;

class CompetitionController extends Controller
{

    public function calculateFullDaysDifference($currentDate, $targetDate)
    {
        $currentDate = new \DateTime($currentDate);
        $targetDate = new \DateTime($targetDate);

        $currentDate->setTime(0, 0, 0);
        $targetDateWithoutTime = clone $targetDate;
        $targetDateWithoutTime->setTime(0, 0, 0);
        $dateDifference = $currentDate->diff($targetDateWithoutTime)->days;

        if ($targetDate < $currentDate) {
            $dateDifference = -$dateDifference;
        }

        $result = [
            'days' => $dateDifference,
            'text' => ''
        ];
        if (abs($dateDifference) > 7) {
            $result['text'] = $targetDate->format('d.m.Y') . ' o ' . $targetDate->format('H:i');
        } elseif ($dateDifference == 1) {
            $result['text'] = 'jutro od ' . $targetDate->format('H:i');
        } elseif ($dateDifference == 0) {
            $result['text'] = 'dzisiaj od ' . $targetDate->format('H:i');
        } elseif ($dateDifference > 0) {
            $result['text'] = 'za ' . $dateDifference . ' dni od ' . $targetDate->format('H:i');
        } else {
            $result['text'] = 'wczoraj od ' . $targetDate->format('H:i');
        }

        return $result;
    }


    public function store(StoreCompetitionRequest $request)
    {
        $input = $request->validated();
        $category = Category::findOrFail($input['category_id']);
        $quiz = new Competition();
        $quiz->category()->associate($category);
        if (isset($input['title'])) $quiz->title = $input['title'];
        if (isset($input['description'])) $quiz->description = $input['description'];
        if (isset($input['time_end'])) $quiz->time_end = $input['time_end'];
        if (isset($input['time_start'])) $quiz->time_start = $input['time_start'];
        if (isset($input['first_points'])) $quiz->first_points = $input['first_points'];
        if (isset($input['second_points'])) $quiz->second_points = $input['second_points'];
        if (isset($input['third_points'])) $quiz->third_points = $input['third_points'];
        if (isset($input['difficulty'])) $quiz->difficulty = $input['difficulty'];
        if (isset($input['image']) && $input['image'] != NULL) {
            $quiz->image = Storage::disk('quiz_images')->url($input['image']->store('', 'quiz_images'));
        }
        $quiz->save();
        $quiz->refresh();
        unset($quiz->category);
        return response()->json([
            'competition_id' => $quiz->id,
        ], 200);
    }


    public function addQuestions(Request $request, int $id)
    {
        $questions = $request->input('questions'); // Pobiera tablicę 'questions' z żądania

        if (is_array($questions)) {
            // Aktualizacja rekordów w tabeli 'questions'
            Question::whereIn('id', $questions)->update(['competition_id' => $id]);

            return response()->json([
                'success' => true,
                'updated_questions' => $questions,
                'competition_id' => $id,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid data. Expected an array of question IDs.',
        ], 400);
    }

    public function allCompetitions(Request $request)
    {
        $user = User::findOrFail(auth()->id());
        $perPage = intval($request->input('per_page', 14));
        $page = intval($request->input('page', 1));
        $currentTime = Carbon::now();

        $difficultyMapping = [
            'easy' => 'Łatwy',
            'medium' => 'Średni',
            'hard' => 'Trudny',
        ];

        // Pobierz ID konkursów, które użytkownik już przesłał
        $submittedCompetitionIds = CompetitionSubmission::where('user_id', $user->id)
            ->pluck('competition_id')
            ->toArray();

        // Pobierz konkursy, które nie zostały przesłane przez użytkownika
        // $allCompetitionsQuery = Competition::where('time_end', '>', $currentTime)
        //     ->whereNotIn('id', $submittedCompetitionIds);

        $allCompetitionsQuery = Competition::where('time_end', '>', $currentTime);

        // Użyj paginacji
        $paginatedCompetitions = $allCompetitionsQuery->paginate($perPage, ['*'], 'page', $page);

        // Mapowanie konkursów
        $mappedUserGames = $paginatedCompetitions->map(function ($competition) use ($difficultyMapping, $currentTime) {
            $category = Category::find($competition->category_id);
            $timeEndCompetition = new \DateTime($competition->time_end);
            $timeStartCompetition = new \DateTime($competition->time_start);

            // Sprawdź, czy obecny czas jest pomiędzy time_start a time_end
            $isTime = $currentTime->between(
                Carbon::parse($competition->time_start),
                Carbon::parse($competition->time_end)
            );

            return [
                'id' => $competition->id,
                'title' => $competition->title ?? null,
                'category' => $category->name ?? null,
                'image' => $competition->image ?? null,
                'awward' => [
                    'first_points' => $competition->first_points,
                    'second_points' => $competition->second_points,
                       'third_points' => $competition->third_points,
                ],
                'fdsfsdf'=>'fdffdss',
                'description' => $competition->description ?? null,
                'date' => $this->calculateFullDaysDifference($currentTime, $competition->time_start)['text'],
                'time_end' => $timeEndCompetition->format('H:i'),
                'time' => [
                    'data' => $timeStartCompetition->format('d.m.Y'),
                    'start_format' => $timeStartCompetition->format('H:i'),
                    'end_format' => $timeEndCompetition->format('H:i'),
                    'start' => $competition->time_start,
                    'end' => $competition->time_end,
                ],
                'difficulty' => $difficultyMapping[$competition->difficulty],
                'questions_count' => $competition->questions_count ?? null,
                'isTime' => $isTime, // Dodane pole
            ];
        });

        return response()->json([
            'data' => $mappedUserGames,
            'pagination' => [
                'per_page' => $paginatedCompetitions->perPage(),
                'count' => $paginatedCompetitions->total(),
                'current_page' => $paginatedCompetitions->currentPage(),
                'last_page' => $paginatedCompetitions->lastPage(),
            ],
        ], 200);
    }


    // public function allCompetitions(Request $request)
    // {
    //     $user = User::findOrFail(auth()->id());
    //     $perPage = intval($request->input('per_page', 14));
    //     $page = intval($request->input('page', 1));
    //     $currentTime = Carbon::now();

    //     $difficultyMapping = [
    //         'easy' => 'Łatwy',
    //         'medium' => 'Średni',
    //         'hard' => 'Trudny',
    //     ];

    //     // Pobierz ID konkursów, które użytkownik już przesłał
    //     $submittedCompetitionIds = CompetitionSubmission::where('user_id', $user->id)
    //         ->pluck('competition_id')
    //         ->toArray();

    //     // Pobierz konkursy, które nie zostały przesłane przez użytkownika
    //     $allCompetitionsQuery = Competition::where('time_end', '>', $currentTime)
    //     ->whereNotIn('id', $submittedCompetitionIds);

    //     // Użyj paginacji
    //     $paginatedCompetitions = $allCompetitionsQuery->paginate($perPage, ['*'], 'page', $page);

    //     // Mapowanie konkursów
    //     $mappedUserGames = $paginatedCompetitions->map(function ($competition) use ($difficultyMapping, $currentTime) {
    //             $category = Category::find($competition->category_id);
    //             $timeEndCompetition = new \DateTime($competition->time_end);
    //             $timeStartCompetition = new \DateTime($competition->time_start);

    //             return [
    //                 'id' => $competition->id,
    //                 'title' => $competition->title ?? null,
    //                 'category' => $category->name ?? null,
    //                 'image' => $competition->image ?? null,
    //                 'awward'=>[
    //                     'first_points' => $competition->first_points,
    //                    'second_points' => $competition->second_points,
    //                 ],
    //                 'description' => $competition->description ?? null,
    //                 'date' => $this->calculateFullDaysDifference($currentTime, $competition->time_start)['text'],
    //                 'time_end' => $timeEndCompetition->format('H:i'),
    //                 'time' => [
    //                     'data' => $timeStartCompetition->format('d.m.Y'),
    //                     'start_format' => $timeStartCompetition->format('H:i'),
    //                     'end_format' => $timeEndCompetition->format('H:i'),
    //                     'start' => $competition->time_start,
    //                     'end' => $competition->time_end,
    //                 ],
    //                 'difficulty' => $difficultyMapping[$competition->difficulty],
    //                 'questions_count' => $competition->questions_count ?? null,
    //             ];
    //         });

    //     return response()->json([
    //         'data' => $mappedUserGames,
    //         'pagination' => [
    //             'per_page' => $paginatedCompetitions->perPage(),
    //             'count' => $paginatedCompetitions->total(),
    //             'current_page' => $paginatedCompetitions->currentPage(),
    //             'last_page' => $paginatedCompetitions->lastPage(),
    //         ],
    //     ], 200);
    // }

    public function isCompetitions(): \Illuminate\Http\JsonResponse
    {
        $userId = auth()->id();

        $hasCompetitions = CompetitionSubmission::where('user_id', $userId)->exists();

        return response()->json([
            'has_competitions' => $hasCompetitions,
        ]);
    }

    public function userCompetitions(Request $request)
    {
        $perPage = intval($request->input('per_page', 14));
        $page = $request->input('page', 1);
        $currentTime = Carbon::now();
        $user = User::findOrFail(auth()->id());

        $difficultyMapping = [
            'easy' => 'Łatwy',
            'medium' => 'Średni',
            'hard' => 'Trudny',
        ];

        $userGames = CompetitionSubmission::with('competition')
            ->where('user_id', $user->id)
            ->paginate($perPage, ['*'], 'page', $page);

        $mappedUserGames = $userGames->items();

        $mappedUserGames = array_map(function ($submission) use ($difficultyMapping, $currentTime) {
            $category = Category::findOrFail($submission->competition->category_id);
            $timeEndCompetition = new \DateTime($submission->competition->time_end);
            $timeStartCompetition = new \DateTime($submission->competition->time_start);


            return [
                'id' => $submission->id,
                'correct_answers' => $submission->correct_answers,
                'title' => $submission->competition->title ?? null,
                'is_finished' => $currentTime->toDateTimeString() < $submission->competition->time_end ? false : true,
                'category' => $submission->competition->category_id ?? null,
                'category' => $category->name ?? null,
                'image' => $submission->competition->image ?? null,
                'description' => $submission->competition->description ?? null,
                // 'date' => $this->calculateFullDaysDifference($currentTime, $competition->time_start)['text'],
                // 'time_end' => $timeEndCompetition->format('H:i'),
                'time' => [
                    'date' => $timeStartCompetition->format('d.m.Y'),
                    'start_format' => $timeStartCompetition->format('H:i'),
                    'end_format' => $timeEndCompetition->format('H:i'),
                    'start' => $submission->competition->time_start,
                    'end' => $submission->competition->time_end,

                ],
                'place' => $submission->place,
                'difficulty' => $difficultyMapping[$submission->competition->difficulty] ?? $submission->competition->difficulty,
                'questions_count' => $submission->competition->questions_count ?? null,
            ];
        }, $mappedUserGames);

        return response()->json([
            'data' => $mappedUserGames,
            'pagination' => [
                'per_page' => $userGames->perPage(),
                'count' => $userGames->total(),
                'current_page' => $userGames->currentPage(),
                'last_page' => $userGames->lastPage(),
            ],
        ], 200);
    }
}