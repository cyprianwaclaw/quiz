<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;
use App\Models\Competition;
use App\Http\Requests\StoreQuizRequest;
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
        }
        elseif ($dateDifference == 1) {
            $result['text'] = 'jutro o ' . $targetDate->format('H:i');
        }
        elseif ($dateDifference == 0) {
            $result['text'] = 'dzisiaj o ' . $targetDate->format('H:i');
        }
        elseif ($dateDifference > 0) {
            $result['text'] = 'za ' . $dateDifference . ' dni o ' . $targetDate->format('H:i');
        }
        else {
            $result['text'] = 'wczoraj o ' . $targetDate->format('H:i');
        }

        return $result;
    }


    public function store(StoreQuizRequest $request)
    {
        $input = $request->validated();
        $category = Category::findOrFail($input['category_id']);
        $quiz = new Competition();
        $user = User::findOrFail(auth()->id());
        $quiz->user()->associate($user);
        $quiz->category()->associate($category);
        if (isset($input['title'])) $quiz->title = $input['title'];
        if (isset($input['description'])) $quiz->description = $input['description'];
        if (isset($input['time'])) $quiz->time = $input['time'];
        if (isset($input['date'])) $quiz->time = $input['date'];
        if (isset($input['difficulty'])) $quiz->difficulty = $input['difficulty'];
        if (isset($input['image']) && $input['image'] != NULL) {
            $quiz->image = Storage::disk('quiz_images')->url($input['image']->store('', 'quiz_images'));
        }
        $quiz->save();
        $quiz->refresh();
        unset($quiz->category);
        return response()->json([
            // 'current_time' => $currentTime->toDateTimeString()
            'competition_id'=> $quiz->id,
            // 'data' => $mappedUserGames,
        ], 200);
    }

    public function allCompetitions(Request $request)
    {
        $perPage = intval($request->input('per_page', 14));
        $page = $request->input('page', 1);
        $currentTime = Carbon::now();

        $difficultyMapping = [
            'easy' => 'Łatwy',
            'medium' => 'Średni',
            'hard' => 'Trudny',
        ];

        $allCompetition = Competition::where('date', '>', $currentTime)->paginate($perPage, ['*'], 'page', $page);
        $mappedAllCompetition = $allCompetition->items();

        $mappedUserGames = array_map(function ($competition) use ($difficultyMapping, $currentTime) {
            $category = Category::findOrFail($competition->category_id);
            return [
                'id' => $competition->id,
                'title' => $competition->title ?? null,
                'category' => $category->name ?? null,
                'image' => $competition->image ?? null,
                'description' => $competition->description ?? null,
                'time' => $competition->time ?? null,
                'date' => $this->calculateFullDaysDifference($currentTime, $competition->date)['text'],
                'difficulty' => $difficultyMapping[$competition->difficulty],
                'questions_count' => $competition->questions_count ?? null,
            ];
        }, $mappedAllCompetition);

        return response()->json([
            // 'current_time' => $currentTime->toDateTimeString(),
            'data' => $mappedUserGames,
            'pagination' => [
                'per_page' => $allCompetition->perPage(),
                'count' => $allCompetition->total(),
                'current_page' => $allCompetition->currentPage(),
                'last_page' => $allCompetition->lastPage(),
            ],
        ], 200);
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
            return [
                'id' => $submission->id,
                'correct_answers' => $submission->correct_answers,
                'title' => $submission->competition->title ?? null,
                'is_finished' => $currentTime->toDateTimeString() < $submission->competition->time ? false : true,
                'category' => $submission->competition->category_id ?? null,
                'category' => $category->name ?? null,
                'image' => $submission->competition->image ?? null,
                'description' => $submission->competition->description ?? null,
                'time' => $submission->competition->time ?? null,
                'date' => \Carbon\Carbon::parse($submission->competition->date)->format('d.m.Y'),
                'difficulty' => $difficultyMapping[$submission->competition->difficulty] ?? $submission->competition->difficulty,
                'questions_count' => $submission->competition->questions_count ?? null,
            ];
        }, $mappedUserGames);

        return response()->json([
            // 'current_time' => $currentTime->toDateTimeString(),
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
