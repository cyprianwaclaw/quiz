<?php

namespace App\Http\Controllers\API;

use App\Models\Quiz;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Filters\QuizesFilter;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\QuizResource;
use App\Http\Requests\IndexQuizRequest;
use App\Http\Requests\StoreQuizRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateQuizRequest;
use App\Http\Controllers\API\APIController;
use App\Http\Requests\FastTwoIndexQuizRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;

/**
 * @group Quizzes
 *
 * Endpoints about `quiz` objects
 */
class QuizController extends APIController
{

    private function orderedBy($orderByTime, $orderByQuestions, $date)
    {
        if ($orderByTime === 'desc') {
            return ['time', 'desc'];
        } elseif ($orderByTime === 'asc') {
            return ['time', 'asc'];
        } elseif ($orderByQuestions === 'desc') {
            return ['questions_count', 'desc'];
        } elseif ($orderByQuestions === 'asc') {
            return ['questions_count', 'asc'];
        } elseif ($date === 'asc') {
            return ['created_at', 'asc'];
        } elseif ($date === 'desc') {
            return ['created_at', 'desc'];
        }
    }


    private function formatDate($publishedAt)
    {
        $now = Carbon::now();
        $publishedAt = Carbon::parse($publishedAt);
        $dateDifference = $publishedAt->diffInDays($now);

        if ($dateDifference === 0) {
            return 'dzisiaj o ' . $publishedAt->format('H:i');
        } elseif ($dateDifference === 1) {
            return 'wczoraj o ' . $publishedAt->format('H:i');
        } elseif ($dateDifference <= 14) {
            return $dateDifference . ' dni temu';
        } else {
            return $publishedAt->format('d.m.Y');
        }
    }

    private function mapQuiz($quiz)
    {
        $difficultyMapping = [
            'easy' => 'Łatwy',
            'medium' => 'Średni',
            'hard' => 'Trudny',
        ];

        $difficulty = str_replace(array_keys($difficultyMapping), array_values($difficultyMapping), $quiz->difficulty);

        return [
            'id' => $quiz->id,
            'title' => $quiz->title,
            'image' => $quiz->image,
            'description' => $quiz->description,
            'difficulty' => $difficulty,
            'time' => $quiz->time,
            'questions_count' => $quiz->questions_count,
            'category' => $quiz->category->name,
            'date' => $this->formatDate($quiz->created_at),
        ];
    }

public function getAll1(Request $request)
{
    // Pobierz wszystkie parametry z zapytania
    $params = $request->only([
        'per_page',
        'page',
        'category',
        'difficulty',
        'is_active',
        'question_count_from',
        'question_count_to',
        'time_from',
        'time_to',
        'order_by_questions',
        'order_by_time',
        'order_by_date',
    ]);

    // Konwersja ciągów znaków z przecinkami na tablice
    foreach (['category', 'difficulty', 'is_active'] as $key) {
        if (isset($params[$key])) {
            $params[$key] = explode(',', $params[$key]);
        }
    }

    // Utwórz zapytanie do Quizu
    $query = Quiz::withCount('questions');

    // Dodaj warunki do zapytania na podstawie wartości z parametrów
    foreach ($params as $key => $value) {
        if ($value !== null && !in_array($key, ['per_page', 'page'])) {
            if ($key === 'difficulty') {
                // Mapowanie trudności na odpowiednie wartości
                $difficultyMapping = [
                    'Łatwy' => 'easy',
                    'Średni' => 'medium',
                    'Trudny' => 'hard',
                ];
                $mappedDifficulty = array_intersect_key($difficultyMapping, array_flip($value));
                $value = array_values($mappedDifficulty);
            }

            if ($key === 'question_count_from' || $key === 'question_count_to') {
                // Dodaj warunek dla liczby pytań w zależności od wartości z zapytania
                $query->whereHas('questions', function ($query) use ($key, $value) {
                    $query->where('questions.id', $value);
                });
            } else {
                // Dodaj warunek dla pozostałych pól
                $query->whereIn($key, $value);
            }
        }
    }

    // Jeśli jest ustawiona opcja sortowania, zastosuj ją
    if (isset($params['order_by_questions']) || isset($params['order_by_time']) || isset($params['order_by_date'])) {
        $query->orderBy(...$this->orderedBy(
            $params['order_by_time'],
            $params['order_by_questions'],
            $params['order_by_date']
        ));
    }

    // Wykonaj paginację
    $quizzes = $query->paginate($params['per_page'] ?? 14, ['*'], 'page', $params['page'] ?? 1);

    // Mapowanie quizów
    $mappedQuizzes = $quizzes->map(function ($quiz) {
        return $this->mapQuiz($quiz);
    });

    // Zwróć odpowiedź JSON
    return response()->json([
        "data" => $mappedQuizzes,
        'pagination' => [
            'per_page' => $quizzes->perPage(),
            'count' => $quizzes->total(),
            'current_page' => $quizzes->currentPage(),
            'last_page' => $quizzes->lastPage(),
        ],
    ]);
}


    public function getAll(Request $request)
    {
        $perPage = intval($request->input('per_page', 14));
        $page = $request->input('page', 1);

        $categories = $request->input('category');
        if ($categories !== null) {
            $categories = explode(',', $categories); // Rozdziel kategorie po przecinkach
        }
        $difficulty = $request->input('difficulty');
        if ($difficulty !== null) {
            $difficulty = explode(',', $difficulty); // Rozdziel kategorie po przecinkach
        }
        $active = $request->input('is_active');
        if ($active !== null) {
            $active = explode(',', $active); // Rozdziel kategorie po przecinkach
        }

        $questionCountFrom = $request->input('question_count_from');
        $questionCountTo = $request->input('question_count_to');
        $timeFrom = $request->input('time_from');
        $timeTo = $request->input('time_to');

        $orderByQuestions = $request->input('order_by_questions');
        $orderByTime = $request->input('order_by_time');
        $orderByDate = $request->input('order_by_date');

        $query = Quiz::withCount('questions');

        if ($orderByQuestions || $orderByTime || $orderByDate) {
            $query->orderBy(...$this->orderedBy($orderByTime, $orderByQuestions, $orderByDate));
        }

        if (!empty($categories)) {
            $query->whereHas('category', function ($query) use ($categories) {
                $query->whereIn('name', $categories);
            });
        }

        if (!empty($difficulty)) {
            $difficultyMapping = [
                'Łatwy' => 'easy',
                'Średni' => 'medium',
                'Trudny' => 'hard',
            ];

            $mappedDifficulty = array_intersect_key($difficultyMapping, array_flip($difficulty));
            $difficultyValues = array_values($mappedDifficulty);

            $query->whereIn('difficulty', $difficultyValues);
        }

        if (!empty($active)) {
            $query->whereIn('is_active', $active);
        }

        if ($timeFrom !== null) {
            $query->where('time', '>=', $timeFrom);
        }
        if ($timeTo !== null) {
            $query->where('time', '<=', $timeTo);
        }

        if ($questionCountFrom !== null || $questionCountTo !== null) {
            $query->whereHas('questions', function ($query) use ($questionCountFrom, $questionCountTo) {
                if ($questionCountFrom !== null) {
                    $query->where('questions.id', '>=', $questionCountFrom);
                }
                if ($questionCountTo !== null) {
                    $query->where('questions.id', '<=', $questionCountTo);
                }
            });
        }

        $quizzes = $query->paginate($perPage, ['*'], 'page', $page);

        $mappedQuizzes = $quizzes->map(function ($quiz) {
            return $this->mapQuiz($quiz);
        });

        return response()->json([
            "data" => $mappedQuizzes,
            'pagination' => [
                'per_page' => $quizzes->perPage(),
                'count' => $quizzes->total(),
                'current_page' => $quizzes->currentPage(),
                'last_page' => $quizzes->lastPage(),
            ],
            // 'allInputs' => $request->input(),
        ]);
    }

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
    // public function getAll(IndexQuizRequest $request)
    // {
    //     $collection = Quiz::filter()->paginate($this->perPage);
    //     return $this->sendCollection($collection);
    // }

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
        if (isset($input['title'])) $quiz->title = $input['title'];
        if (isset($input['description'])) $quiz->description = $input['description'];
        if (isset($input['time'])) $quiz->time = $input['time'];
        if (isset($input['difficulty'])) $quiz->difficulty = $input['difficulty'];
        if (isset($input['image']) && $input['image'] != NULL) {
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
        if (isset($input['category_id'])) {
            $category = Category::findOrFail($input['category_id']);
            $quiz->category()->associate($category);
        }
        if (isset($input['title'])) $quiz->title = $input['title'];
        if (isset($input['description'])) $quiz->description = $input['description'];
        if (isset($input['image']) && $input['image'] != NULL) {
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
            return $this->sendResponse(null, null, 304);
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
        if ($quiz->save())
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
        if ($quiz->save())
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

        $mappedQuizzes = $collection->map(function ($quiz) {
            return $this->mapQuiz($quiz);
        });

        return response([
            'data' => $mappedQuizzes,
        ], 200);
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

        $mappedQuizzes = $collection->map(function ($quiz) {
            return $this->mapQuiz($quiz);
        });

        return response([
            'data' => $mappedQuizzes,
        ], 200);
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

        $mappedQuizzes = $collection->map(function ($quiz) {
            return $this->mapQuiz($quiz);
        });

        return response([
            'data' => $mappedQuizzes,
        ], 200);
    }
}
