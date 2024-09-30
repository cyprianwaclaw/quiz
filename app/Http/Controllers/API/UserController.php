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

    public function getInvitedUsers1(Request $request)
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
            // 'data' => $plan,
            'data' => $invited,
            'pagination' => [
                'per_page' => $invited->perPage(),
                'count' => $invited->total(),
                'current_page' => $invited->currentPage(),
                'last_page' => $invited->lastPage(),
            ],
        ], 200);
    }



    public function getInvitedUsers(Request $request)
    {
        $user = auth()->user();
        $perPage = 6;
        $page = $request->input('page', 1);

        $invited = User::select('id', 'name', 'email', 'created_at', 'updated_at', 'avatar_path')
            ->with(['planSubscriptions' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->where('invited_by', $user->id)
            ->paginate($perPage, ['*'], 'page', $page);

        $mappedInvited = $invited->map(function ($invitedUser) {
            $latestSubscription = $invitedUser->planSubscriptions->first();
            return [
                'name' => $invitedUser->name,
                'surname' => $invitedUser->surname,
                'points' => $invitedUser->points ? $invitedUser->points : 0,
                'email' => $invitedUser->email,
                'avatar' => $invitedUser->avatar_path,
                // !do poprawy
                'is_premium' => isset($latestSubscription->id) ? true : false,
            ];
        });

        return response([
            'data' => $mappedInvited,
            'pagination' => [
                'per_page' => $invited->perPage(),
                'count' => $invited->total(),
                'current_page' => $invited->currentPage(),
                'last_page' => $invited->lastPage(),
            ],
        ], 200);
    }

//     /**
//      * Get list of logged user's quizzes objects
//      * @group Operation about user
//      *
//      * Also return header response `X-Total-Count` containing the number of fetched objects.
//      *
//      * @responseFile status=200 scenario="Quiz fetched" storage/api-docs/responses/quizzes/my-quizzes.200.json
//      * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
//      */
// public function getUserQuizzes(Request $request)
// {
//     $user = auth()->user();
//     $perPage = 6;
//     $page = $request->input('page', 1);
//     $status = $request->input('status');

//     $query = Quiz::where('invited_by', $user->id);

//     if ($status !== null) {
//         $query->where('is_active', $status);
//     }

//     $userQuizzes = $query->paginate($perPage, ['*'], 'page', $page);

//     return response()->json([
//         'data' => $userQuizzes->items(),
//         'status' => $status,
//         'pagination' => [
//             'total' => $userQuizzes->total(),
//             'count' => $userQuizzes->count(),
//             'per_page' => $userQuizzes->perPage(),
//             'current_page' => $userQuizzes->currentPage(),
//             'total_pages' => $userQuizzes->lastPage()
//         ]
//     ]);

// }
}

// public function answer_question(AnswerQuestionRequest $request, QuizSubmission $quizSubmission)
//     {
//         $user = User::find(auth()->id());
//         $validated = $request->validated();
//         $question = Question::findOrFail($validated['question_id']);
//         $submissionAnswer = SubmissionAnswer::firstWhere([
//             ['question_id', '=', $question->id],
//             ['quiz_submission_id', '=', $quizSubmission->id],
//         ]);
//         $answer = Answer::findOrFail($validated['answer_id']);
//         //check if user has verify yourself e-mail
//      //   if ($user->cannot('update', [$submissionAnswer, $answer])) {
//       //    return $this->sendError( 'Unauthorized','You cannot do this',401);
//      //}

//         if($submissionAnswer->answer()->associate($answer)->save()){
//              $response = [
//                'submission_id' => $quizSubmission->id,
//          'is_correct' => $answer->correct,
//                'next_question' => $this->getNextQuestion($quizSubmission),
//          ];

//            if (is_null($response['next_question'])) {
//              $quizDuration = gmdate('i:s', $quizSubmission->created_at->diffInSeconds($quizSubmission->ended_at));
//               $response['quiz_time'] = $quizDuration;
//            }
//      return $this->sendResponse($response);
//         }


//     }
