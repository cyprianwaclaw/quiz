<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\CategoryResource;
use App\Models\Answer;
use App\Models\AnswerUser;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @group Categories
 *
 * Endpoints about `category` objects
 */
class CategoryController extends APIController
{
    /**
     * Get list of category objects
     *
     * Also return header response `X-Total-Count` containing the number of fetched objects.
     *
     * @responseFile status=200 scenario="Category fetched" storage/api-docs/responses/categories/index.200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
     */
    public function index()
    {
        $collection = Category::select('id', 'name')->get();
        return response([
            // 'success' => true,
            'data' => $collection,
            // 'message' => 'Objects fetched',
            // 'count' => $collection->count()
            ],
            200);
    }

    /**
     * Store new category
     *
     * @responseFile status=201 scenario="Category created" storage/api-docs/responses/categories/store.201.json
     * @responseFile status=422 scenario="Validation error" storage/api-docs/responses/error.422.json
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->validate([
            // Example: Nowa kategoria
            'name' => 'required|min:3',
        ]);
        $category = Category::create($input);
        return $this->sendResponse(new CategoryResource($category), 'Object created.', 201);
    }

    /**
     * Return specific category by ID
     *
     * @urlParam id integer required The ID of the category. Example: 1
     * @responseFile 200 scenario="Object fetched" storage/api-docs/responses/categories/show.200.json
     * @responseFile 404 scenario="Object not found" storage/api-docs/responses/categories/show.404.json
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $category = Category::find($id);
        if (is_null($category)) {
            return $this->sendError('Object does not exist.');
        }
        return $this->sendResponse(new CategoryResource($category), 'Object fetched.');
    }

    /**
     * Update specific category by ID
     *
     * @urlParam id integer required The ID of the category. Example: 1
     * @responseFile 200 scenario="Category fetched" storage/api-docs/responses/categories/update.200.json
     * @response 304 scenario="Category not updated" [Empty response]
     * @responseFile 422 scenario="Validation error" storage/api-docs/responses/error.422.json
     * @param Request $request
     * @param Category $category
     */
    public function update(Request $request, Category $category)
    {
        $input = $request->validate([
            // Example: Kategoria
            'name' => 'required|min:3',
        ]);
        $category->name = $input['name'];
        $category->save();
        if($category->wasChanged())
            return $this->sendResponse(new CategoryResource($category), 'Object updated.');
        else
            return response(null,304);
    }

    /**
     * Delete specific category by ID
     *
     * @response 204 scenario="Category deleted" [Empty response]
     * @responseFile 404 scenario="Category not found" storage/api-docs/responses/categories/delete.404.json
     * @urlParam id integer required The ID of the category. Example: 4
     *
     * @param Category $category
     */
    public function destroy(Category $category)
    {
        foreach ($category->questions as $question) {
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
        }
        $category->delete();
        return $this->sendResponse(null, null, 204);
    }
}
