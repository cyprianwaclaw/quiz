<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\PlanResource;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{

    /**
     * Get list of plan objects
     *
     * Also return header response `X-Total-Count` containing the number of fetched objects.
     *
     * @group Plans
     * @responseFile status=200 scenario="Plan fetched" storage/api-docs/responses/plans/index.200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/api-docs/responses/401.json
     */
    public function index()
    {
        $collection = Plan::where('price', '>', 0)->get();
        return response([
            'success' => true,
            'data' => PlanResource::collection($collection),
            'message' => 'Objects fetched',
            'count' => $collection->count()
            ],
            200,[
                'X-Total-Count' => $collection->count()
            ]
        );
    }
}
