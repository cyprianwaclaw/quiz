<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\API\UserInvitedResource;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends APIController
{
    /**
     * Get list of invited users
     * @group Operation about user
     *
     * @responseFile 200 scenario="Success" storage/api-docs/responses/users/getInvitedUsers.200.json
     * @param User|null $user
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    // public function getInvitedUsers(Request $request)
    // {
    //     $user = auth()->user();
    //     $perPage = 10;
    //     $page = $request->input('page', 1);

    //     // Wybierz tylko interesujące cię kolumny
    //     $invited = User::select('id', 'name', 'email', 'created_at', 'updated_at', 'avatar_path')
    //         ->where('invited_by', $user->id)
    //         ->paginate($perPage, ['*'], 'page', $page);

    //     return response([
    //         'success' => true,
    //         'data' => $invited,
    //         'message' => 'Invited users fetched',
    //     ], 200);
    // }

    public function getInvitedUsers(Request $request)
    {
        $user = auth()->user();
        $perPage = 12;
        $page = $request->input('page', 1);
$plan = Plan::all();
        // Wybierz tylko interesujące cię kolumny
        $invited = User::select('id', 'name', 'email', 'created_at', 'updated_at', 'avatar_path')
        ->with(['planSubscriptions' => function ($query) {
            $query->orderBy('created_at', 'desc')->first();
        }])
        // planSubscriptions->last()->active()
            ->where('invited_by', $user->id)
            ->paginate($perPage, ['*'], 'page', $page);
        // $invited->each(function ($user) {
        //     $user->is_premium = optional($user->planSubscriptions->first())->active ?? false;
        // });
        return response([
            'success' => true,
            // 'data' => $plan,
            'data' => $invited,
            'message' => 'Invited users fetched',
        ], 200);
    }

    /**
     * Get list of logged user's quizzes objects
     * @group Operation about user
     *
     * Also return header response `X-Total-Count` containing the number of fetched objects.
     *
     * @responseFile status=200 scenario="Quiz fetched" storage/api-docs/responses/quizzes/my-quizzes.200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
     */
    public function getUserQuizzes()
    {
        $collection = auth()->user()->quizzes;
        return response(
            [
                'success' => true,
                'data' => $collection,
                'message' => 'Objects fetched',
                'count' => $collection->count()
            ],
            200,
            [
                'X-Total-Count' => $collection->count()
            ]
        );
    }
}