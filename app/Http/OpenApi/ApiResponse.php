<?php

namespace App\Http\OpenApi;

/**
 * @OA\Schema
 */
class ApiResponse
{

    /**
     * @OA\Property
     *
     * @var boolean
     */
    public $success;

    /**
     * @OA\Property
     *
     * @var mixed[]
     */
    public $data;

    /**
     * @OA\Property
     *
     * @var string
     */
    public $message;
}
