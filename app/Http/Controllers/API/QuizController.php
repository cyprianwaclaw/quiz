<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\FastTwoIndexQuizRequest;
use App\Http\Requests\IndexQuizRequest;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Http\Resources\QuizResource;
use App\Models\Category;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

/**
 * @group Quizzes
 *
 * Endpoints about `quiz` objects
 */
class QuizController extends APIController
{
    /**
     * Get list of active quiz objects
     *
     * Allow sort, filter and search collection like as
     *
     * api/quizzes?sort[0]=time,desc&sort[1]=difficulty,desc&search=znasz
     *
     * Also return header response `X-Total-Count` containing the number of fetched objects.
     *
     * @responseFile status=200 scenario="Quiz fetched" storage/api-docs/responses/quizzes/index.200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
     */
    public function index(IndexQuizRequest $request)
    {
        $collection = Quiz::active()->filter()->paginate($this->perPage);
        return $this->sendCollection($collection);
    }

    /**
     * Get list of inactive quiz objects
     *
     * Allow sort, filter and search collection like as
     *
     * api/quizzes?sort[0]=time,desc&sort[1]=difficulty,desc&search=znasz
     *
     * Also return header response `X-Total-Count` containing the number of fetched objects.
     *
     * @responseFile status=200 scenario="Quiz fetched" storage/api-docs/responses/quizzes/index.200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
     */
    public function getInactive(IndexQuizRequest $request)
    {
        $collection = Quiz::inactive()->filter($request)->paginate($this->perPage);
        return $this->sendCollection($collection);
    }

    /**
     * Get list of all quiz objects
     *
     * Allow sort, filter and search collection like as
     *
     * api/quizzes?sort[0]=time,desc&sort[1]=difficulty,desc&search=znasz
     *
     * Also return header response `X-Total-Count` containing the number of fetched objects.
     *
     * @responseFile status=200 scenario="Quiz fetched" storage/api-docs/responses/quizzes/index.200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
     */
    public function getAll(IndexQuizRequest $request)
    {
        $collection = Quiz::filter()->paginate($this->perPage);
        return $this->sendCollection($collection);
    }

    /**
     * Return specific quiz by ID
     *
     * @urlParam id integer required The ID of the quiz. Example: 2
     * @responseFile 200 scenario="Quiz fetched" storage/api-docs/responses/quizzes/show.200.json
     * @responseFile 404 scenario="Quiz not found" storage/api-docs/responses/resource.404.json
     *
     * @param Quiz $quiz
     * @return JsonResponse
     */
    public function show(Quiz $quiz): JsonResponse
    {
        return $this->sendResponse(new QuizResource($quiz), 'Object fetched.');
    }

    /**
     * Store new quiz
     *
     * @responseFile status=201 scenario="Quiz created" storage/api-docs/responses/quizzes/store.201.json
     * @responseFile status=422 scenario="Validation error" storage/api-docs/responses/error.422.json
     * @param StoreQuizRequest $request
     * @return JsonResponse
     */
    public function store(StoreQuizRequest $request): JsonResponse
    {
        $input = $request->validated();
        $category = Category::findOrFail($input['category_id']);
        $quiz = new Quiz();
        $user = User::findOrFail(auth()->id());
        $quiz->user()->associate($user);
        $quiz->category()->associate($category);
        if(isset($input['title'])) $quiz->title = $input['title'];
        if(isset($input['description'])) $quiz->description = $input['description'];
        if(isset($input['time'])) $quiz->time = $input['time'];
        if(isset($input['difficulty'])) $quiz->difficulty = $input['difficulty'];
        if(isset($input['image']) && $input['image'] != NULL) {
            $quiz->image = Storage::disk('quiz_images')->url($input['image']->store('', 'quiz_images'));
        }
        $quiz->save();
        $quiz->refresh();
        unset($quiz->category);
        return $this->sendResponse(new QuizResource($quiz), 'Object created.', 201);
    }

    /**
     * Update specific quiz by ID
     *
     * @responseFile 200 scenario="Quiz fetched" storage/api-docs/responses/quizzes/update.200.json
     * @response 304 scenario="Quiz not updated" No content
     * @responseFile 422 scenario="Validation error" storage/api-docs/responses/error.422.json
     * @param UpdateQuizRequest $request
     * @param Quiz $quiz
     * @return JsonResponse
     */
    public function update(UpdateQuizRequest $request, Quiz $quiz): JsonResponse
    {
        $input = $request->validated();
        if(isset($input['category_id'])) {
            $category = Category::findOrFail($input['category_id']);
            $quiz->category()->associate($category);
        }
        if(isset($input['title'])) $quiz->title = $input['title'];
        if(isset($input['description'])) $quiz->description = $input['description'];
        if(isset($input['image']) && $input['image'] != NULL) {
            if ($quiz->image != NULL) {
                Storage::disk('quiz_images')->delete($quiz->image);
            }
            $quiz->image = Storage::disk('quiz_images')->url($input['image']->store('', 'quiz_images'));
        }
        $quiz->save();
        $quiz->refresh();
        unset($quiz->category);
        if ($quiz->wasChanged())
            return $this->sendResponse(new QuizResource($quiz), 'Object updated.');
        else
            return $this->sendResponse(null,null, 304);
    }

    /**
     * Delete specific quiz by ID
     *
     * **Warning:** This action also delete related questions with answers
     *
     * @response 204 scenario="Quiz deleted" [Empty response]
     * @responseFile 404 scenario="Quiz not found" storage/api-docs/responses/resource.404.json
     * @urlParam id integer required The ID of the quiz. Example: 2
     *
     * @param Quiz $quiz
     * @return JsonResponse
     */
    public function destroy(Quiz $quiz): JsonResponse
    {
        $quiz->quizSubmission->each->delete();
        foreach ($quiz->questions as $question) {
            $question->answers->each->delete();
        }
        $quiz->questions->each->delete();
        $quiz->delete();
        return $this->sendResponse(null, null, 204);
    }

    /**
     * Get list of questions belongs to quiz
     *
     * Also return header response `X-Total-Count` containing the number of fetched objects.
     *
     * @responseFile status=200 scenario="Question fetched" storage/api-docs/responses/questions/index.200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
     *
     * @param Quiz $id
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function getQuestions(Quiz $id)
    {
        $collection = $id->questions;
        return $this->sendCollection($collection);
    }

    /**
     * Activate quiz
     * @group Quizzes
     *
     * @urlParam quiz integer required The ID of the quiz. Example: 1
     * @response 200 scenario="Quiz activated" [Empty response]
     * @responseFile 404 scenario="Quiz not found" storage/api-docs/responses/resource.404.json
     *
     * @param Quiz $quiz
     * @return Response|JsonResponse
     */
    public function activate(Quiz $quiz): Response|JsonResponse
    {
        $quiz->is_active = true;
        if($quiz->save())
            return $this->sendSuccess();
        else
            return $this->sendError('Cannot activate this Question');
    }

    /**
     * Dectivate quiz
     * @group Quizzes
     *
     * @urlParam id integer required The ID of the quiz. Example: 1
     * @response 200 scenario="Quiz deactivated" [Empty response]
     * @responseFile 404 scenario="Quiz not found" storage/api-docs/responses/resource.404.json
     *
     * @param Quiz $quiz
     * @return JsonResponse|Response
     */
    public function deactivate(Quiz $quiz): Response|JsonResponse
    {
        $quiz->is_active = false;
        if($quiz->save())
            return $this->sendSuccess();
        else
            return $this->sendError('Cannot deactivate this Question');
    }

    /**
     * Get random 2 quizzes
     *
     * Also return header response `X-Total-Count` containing the number of fetched objects.
     *
     * @responseFile status=200 scenario="Quiz fetched" storage/api-docs/responses/quizzes/fastTwo.200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
     */
    public function fastTwo()
    {
        $collection = Quiz::inRandomOrder()->active()->limit(2)->get();
        return response([
            'success' => true,
            'data' => $collection,
            'message' => 'Objects fetched',
            'count' => $collection->count()
            ],
            200,[
                'X-Total-Count' => $collection->count()
            ]
        );
    }

    /**
     * Get list of popular quiz objects sorted by solved
     *
     * Allow sort, filter and search collection
     *
     * Also return header response `X-Total-Count` containing the number of fetched objects.
     *
     * @responseFile status=200 scenario="Quiz fetched" storage/api-docs/responses/quizzes/popular.200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
     */
    public function popular(IndexQuizRequest $request)
    {
        $collection = Quiz::popular()->active()->filter($request)->paginate($this->perPage);
        return response([
            'success' => true,
            'data' => $collection,
            'message' => 'Objects fetched',
            'count' => $collection->count()
            ],
            200,[
                'X-Total-Count' => $collection->count()
            ]
        );
    }

    /**
     * Get list of lastest quiz objects sorted by created_at
     *
     * Allow sort, filter and search collection
     *
     * Also return header response `X-Total-Count` containing the number of fetched objects.
     *
     * @responseFile status=200 scenario="Quiz fetched" storage/api-docs/responses/quizzes/popular.200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
     */
    public function latest(IndexQuizRequest $request)
    {
        $collection = Quiz::latest()->active()->filter($request)->paginate($this->perPage);
        return response([
            'success' => true,
            'data' => $collection,
            'message' => 'Objects fetched',
            'count' => $collection->count()
            ],
            200,[
                'X-Total-Count' => $collection->count()
            ]
        );
    }

    /**
     * Get list of quizzes selected for logged user
     *
     * Allow sort, filter and search collection
     *
     * Also return header response `X-Total-Count` containing the number of fetched objects.
     *
     * @responseFile status=200 scenario="Quiz fetched" storage/api-docs/responses/quizzes/forYou.200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
     */
    public function forYou(IndexQuizRequest $request)
    {
        $collection = Quiz::forYou()->active()->filter($request)->paginate($this->perPage);
        return response([
            'success' => true,
            'data' => $collection,
            'message' => 'Objects fetched',
            'count' => $collection->count()
            ],
            200,[
                'X-Total-Count' => $collection->count()
            ]
        );
    }
}
