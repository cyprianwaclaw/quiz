<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class APIController extends Controller
{
    protected $perPage;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->perPage = $request->input('per_page', 15);
    }

    /**
     * success response method.
     *
     * @return JsonResponse 
     */
    public function sendResponse($result = null, $message = null, $status = 200): JsonResponse
    {
        $response = [
            'success' => true
            ];
        if($result) $response['data'] = $result;
        if($message) $response['message'] = $message;

        return response()->json($response, $status);
    }


    /**
     * return error response.
     *
     * @return JsonResponse
     */
    public function sendError($error, $errorMessages = null, $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    /**
     * Send success response with 200 status code
     * @return \Illuminate\Http\Response
     */
    public function sendSuccess()
    {
        return response()->noContent(200);
    }



    protected function sendCollection($collection): Response|Application|ResponseFactory
    {
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
}