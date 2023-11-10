<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreQuestionRequest;
use App\Http\Resources\AnswerResource;
use App\Http\Resources\QuestionResource;
use App\Models\Answer;
use App\Models\AnswerUser;
use App\Models\Question;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * @group Questions
 *
 * Endpoints about `question` objects
 */
class QuestionController extends APIController
{
    /**
     * Get list of question objects
     *
     * Also return header response `X-Total-Count` containing the number of fetched objects.
     *
     * @responseFile status=200 scenario="Question fetched" storage/api-docs/responses/questions/index.200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
     */
    public function index()
    {
        $collection = Question::all();
        return response([
            'success' => true,
            'data' => $collection,
            'message' => 'Objects fetched',
            'count' => $collection->count()
        ],
            200, [
                'X-Total-Count' => $collection->count()
            ]
        );
    }

    /**
     * Store new question
     *
     * @responseFile status=201 scenario="Question created" storage/api-docs/responses/questions/store.201.json
     * @responseFile status=422 scenario="Validation error" storage/api-docs/responses/error.422.json
     * @paramField cat it opis Example: 1
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->validate([
            // Example: Pytanie
            'question' => 'required',
            // Example: 1
            'quiz_id' => 'required|exists:App\Models\Quiz,id',
        ]);
        $question = Question::create($input);
        return $this->sendResponse(new QuestionResource($question), 'Object created.', 201);
    }

    /**
     * Return specific question by ID
     *
     * @urlParam id integer required The ID of the question. Example: 1
     * @responseFile 200 scenario="Question fetched" storage/api-docs/responses/questions/show.200.json
     * @responseFile 404 scenario="Question not found" storage/api-docs/responses/resource.404.json
     *
     * @param $id
     * @return JsonResponse
     */
    public function show(Question $question): JsonResponse
    {
        return $this->sendResponse(new QuestionResource($question), 'Object fetched.');
    }

    /**
     * Update specific question by ID
     *
     * @urlParam id integer required The ID of the question. Example: 1
     * @responseFile 200 scenario="Question fetched" storage/api-docs/responses/questions/update.200.json
     * @response 304 scenario="Question not updated" No content
     * @responseFile 422 scenario="Validation error" storage/api-docs/responses/error.422.json
     * @param Request $request
     * @param Question $question
     */
    public function update(Request $request, Question $question)
    {
        $input = $request->validate([
            // Example: Kategoria
            'question' => 'required|min:3',
            // Example: 1
            'category_id' => 'exists:App\Models\Category,id'
        ]);
        $question->question = $input['question'];
        $question->save();
        if ($question->wasChanged())
            return $this->sendResponse(new QuestionResource($question), 'Object updated.');
        else
            return response(null, 304);
    }

    /**
     * Delete specific question by ID
     *
     * @response 204 scenario="Question deleted" [Empty response]
     * @responseFile 404 scenario="Question not found" storage/api-docs/responses/resource.404.json
     * @responseFile 400 scenario="Bad Request" storage/api-docs/responses/questions/destroy.400.json
     * @urlParam id integer required The ID of the question. Example: 4
     *
     * @param Question $question
     */
    public function destroy(Question $question)
    {
        try {
            $question->deleteOrFail();
        } catch (QueryException $exception){
            return response(['message'=>'Something is wrong. Please check your request'], Response::HTTP_BAD_REQUEST);
            //TODO: musisz najpierw usunąć połączone pytania
        }
        return $this->sendResponse(null, null, 204);
    }
/*
    public function destroy(Question $question): JsonResponse
    {
        $aus = AnswerUser::where('question_id', $question->id)->get();
        foreach ($aus as $au) {
            $au->delete();
        }
        $question->correct_answer_id = null;
        $question->save();
        $answers = Answer::where('question_id', $question->id)->get();
        foreach ($answers as $answer) {
            $answer->delete();
        }
        $question->delete();
        return $this->sendResponse(null, null, 204);
    }
*/

    /**
     * Get list of answers belongs to question
     *
     * Also return header response `X-Total-Count` containing the number of fetched objects.
     *
     * @responseFile status=200 scenario="Question fetched" storage/api-docs/responses/questions/index.200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
     *
     * @param Question $id
     * @return JsonResponse
     */
    public function getAnswers(Question $id)
    {
        $collection = $id->answers;
        return response([
            'success' => true,
            'data' => $collection,
            'message' => 'Objects fetched',
            'count' => $collection->count()
        ],
            200, [
                'X-Total-Count' => $collection->count()
            ]
        );
    }

    /**
     * Delete answers belongs to question
     *
     * Also return header response `X-Total-Count` containing the number of deleted objects.
     *
     * @response 204 scenario="Question deleted" [Empty response]
     * @responseFile 404 scenario="Question not found" storage/api-docs/responses/resource.404.json
     * @responseFile 400 scenario="Bad Request" storage/api-docs/responses/questions/destroy.400.json
     * @urlParam id integer required The ID of the question. Example: 4
     *
     * @param Question $id
     */
    public function destroyAnswers(Question $id)
    {
        $deleted_count = $id->answers()->delete();
        if($deleted_count) {
            return response(null,
                204, [
                    'X-Total-Count' => $deleted_count
                ]
            );
        }
        else {
            return response(null, 304);
        }
    }
}
