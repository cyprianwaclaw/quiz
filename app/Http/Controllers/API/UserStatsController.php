<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserStatsRequest;
use App\Http\Requests\UpdateUserStatsRequest;
use App\Models\UserStats;
use Illuminate\Http\JsonResponse;

class UserStatsController extends APIController
{
    /**
     * Return user stats
     * @group Operation about user
     *
     * @responseFile 200 scenario="Stats fetched" storage/api-docs/responses/users/stats/show.200.json
     * @responseFile 404 scenario="Stats not found" storage/api-docs/responses/resource.404.json
     *
     * @return JsonResponse
     */
    public function show()
    {
        /** @var UserStats $stats */
        $stats = auth()->user()->stats;
        return $this->sendResponse([
            'correct_answers' => $stats->correct_answers,
            'incorrect_answers' => $stats->incorrect_answers,
        ]);
    }
    // /**
    //  * Update user stats after quiz completion
    //  *
    //  * @param bool $isCorrect Whether the last answer was correct
    //  */
    // private function updateStats(bool $isCorrect)
    // {
    //     $user = auth()->user();
    //     $userStats = $user->stats;

    //     if ($isCorrect) {
    //         $userStats->increment('correct_answers');
    //     } else {
    //         $userStats->increment('incorrect_answers');
    //     }

    //     $userStats->save();
    // }
}
