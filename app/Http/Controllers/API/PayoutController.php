<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePayoutRequest;
use App\Http\Requests\UpdatePayoutStatusRequest;
use App\Http\Resources\PayoutResource;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
 * @group Payouts
 */
class PayoutController extends APIController
{
    /**
     * Get list of payout objects
     * @group Operation about user
     *
     * Also return header response `X-Total-Count` containing the number of fetched objects.
     * @urlParam user integer The ID of user. Example: 1
     * @responseFile status=200 scenario="Payout fetched" storage/api-docs/responses/payouts/index.200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
     * @responseFile status=404 scenario="User not found" storage/api-docs/responses/resource.404.json
     */
    public function index(Request $request)
    {

        $user = User::findOrFail(auth()->id());


        $perPage = 14;
        $page = $request->input('page', 1);
        $query = $user->payouts()->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        $statusMap = [
            'in_progress' => 'W oczekiwaniu',
            'success' => 'Sukces',
            'fail' => 'Błąd'
        ];


        $mappedData = $query->getCollection()->map(function ($item) use ($statusMap) {
            return [
                'amount' => $item->amount,
                'status' => $statusMap[$item->status] ?? 'Nieznany',
                // 'created_at' => $item->created_at->toDateTimeString(),
                'date' => $item->created_at->format('d.m.Y'),
            ];
        });
        $query->setCollection($mappedData);

        return response()->json([
            "payouts" => $query->items(),
            'pagination' => [
                'per_page' => $query->perPage(),
                'count' => $query->total(),
                'current_page' => $query->currentPage(),
                'last_page' => $query->lastPage(),
            ],
        ]);
    }

    /**
     * Store new payout
     * @group Operation about user
     * @bodyParam points integer required Number of points to be converted into currency. Example: 1

     * @responseFile status=422 scenario="Validation error" storage/api-docs/responses/error.422.json
     * @param StorePayoutRequest $request
     * @return JsonResponse
     */
    public function store(StorePayoutRequest $request): JsonResponse
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:1',
        ]);
        $this->authorize('create', [Payout::class, $validated['points']]);
        $payout = new Payout();
        $payout->user_id = $request->user()->id;
        $payout->points = $validated['points'];
        auth()->user()->subtractPoints($validated['points']);
        $payout->amount = $validated['points'] * config('game.points_multiplier');
        $payout->save();
        return $this->sendResponse(new PayoutResource($payout), 'Object created.', 201);
    }

    /**
     * Change payout status
     * @group Operation about user
     *
     * @urlParam payout integer required The ID of the payout. Example: 1
     * @response 200 scenario="Status changed" No content
     * @response 304 scenario="Payout not updated" No content
     * @responseFile 422 scenario="Validation error" storage/api-docs/responses/error.422.json
     * @param UpdatePayoutStatusRequest $request
     * @param Payout $payout
     * @return Response|JsonResponse
     * @throws AuthorizationException
     */
    public function setStatus(UpdatePayoutStatusRequest $request, Payout $payout): Response|JsonResponse
    {
        $this->authorize('update', $payout);
        $input = $request->validated();
        $payout->status = $input['status'];
        if ($payout->save())
            if ($payout->wasChanged('status'))
                return $this->sendSuccess();
            else
                return $this->sendError(null, null, 304);
        else
            return $this->sendError('Something went wrong.');
    }

    /**
     * Return specific payout by ID
     *
     * @urlParam id integer required The ID of the payout. Example: 1
     * @responseFile 200 scenario="Payout fetched" storage/api-docs/responses/payouts/show.200.json
     * @responseFile 404 scenario="Payout not found" storage/api-docs/responses/resource.404.json
     *
     * @param $id
     * @return JsonResponse
     */
    public function show(Payout $payout): JsonResponse
    {
        return $this->sendResponse(new PayoutResource($payout), 'Object fetched.');
    }
}
