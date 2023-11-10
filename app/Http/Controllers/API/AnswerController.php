<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\AnswerResource;
use App\Models\Answer;
use App\Http\Requests\StoreAnswerRequest;
use App\Http\Requests\UpdateAnswerRequest;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Answers
 *
 * Endpoints about `category` objects
 */
class AnswerController extends APIController
{
    /**
     * Get list of answer objects
     *
     * Also return header response `X-Total-Count` containing the number of fetched objects.
     *
     * @responseFile status=200 scenario="Answer fetched" storage/api-docs/responses/answers/index.200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
     */
    public function index()
    {
        $collection = Answer::all();
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
     * Store new answer
     *
     * @responseFile status=201 scenario="Answer created" storage/api-docs/responses/answers/store.201.json
     * @responseFile status=422 scenario="Validation error" storage/api-docs/responses/error.422.json
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->validate([
            // Example: Odpowiedz
            'answer' => 'required|min:3',
            // Example: 1
            'question_id' => 'required|exists:App\Models\Question,id',
            // Example: 1
            'correct' => 'boolean'

        ]);
        $answer = Answer::create($input)->refresh();
        return $this->sendResponse(new AnswerResource($answer), 'Object created.', 201);
    }

    /**
     * Return specific answer by ID
     *
     * @urlParam id integer required The ID of the answer. Example: 1
     * @responseFile 200 scenario="Answer fetched" storage/api-docs/responses/answers/show.200.json
     * @responseFile 404 scenario="Answer not found" storage/api-docs/responses/resource.404.json
     *
     * @param $id
     * @return JsonResponse
     */
    public function show(Answer $answer): JsonResponse
    {
        return $this->sendResponse(new AnswerResource($answer), 'Object fetched.');
    }

    /**
     * Update specific answer by ID
     *
     * @urlParam id integer required The ID of the answer. Example: 1
     * @responseFile 200 scenario="Answer fetched" storage/api-docs/responses/answers/update.200.json
     * @response 304 scenario="Answer not updated" No content
     * @responseFile 422 scenario="Validation error" storage/api-docs/responses/error.422.json
     * @param Request $request
     * @param Answer $answer
     */
    public function update(Request $request, Answer $answer)
    {
        $input = $request->validate([
            // Example: Odpowiedz
            'answer' => 'min:3',
            // Example: 1
            'question_id' => 'exists:App\Models\Question,id'
        ]);
        $answer->fill($input);
        $answer->save();
        if ($answer->wasChanged())
            return $this->sendResponse(new AnswerResource($answer), 'Object updated.');
        else
            return response(null, 304);
    }

    /**
     * Delete specific answer by ID
     *
     * @response 204 scenario="Answer deleted" [Empty response]
     * @responseFile 404 scenario="Answer not found" storage/api-docs/responses/resource.404.json
     * @urlParam id integer required The ID of the answer. Example: 4
     *
     * @param Answer $answer
     */
    public function destroy(Answer $answer)
    {
        $answer->delete();
        return $this->sendResponse(null, null, 204);
    }
    /**
     * Get question which has specific answer
     *
     * @responseFile 200 scenario="Answer fetched" storage/api-docs/responses/answers/show.200.json
     * @responseFile 404 scenario="Answer not found" storage/api-docs/responses/resource.404.json
     *
     * @return JsonResponse
     */
    public function getQuestion(Answer $id)
    {
        $answer = $id;
        return response()->json($id->question);
    }
}
