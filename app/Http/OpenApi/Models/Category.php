<?php

namespace App\Http\OpenApi\Models;

/**
 * @OA\Schema(
 *     @OA\Xml(
 *         name="Category"
 *     )
 * )
 */
class Category
{

    /**
     * @OA\Property
     * @var int
     */
    public $id;

    /**
     * @OA\Property(
     *     example="Zwierzęta",
     * )
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *     ref="#/components/schemas/DateTime"
     * )
     */
    public $created_at;

    /**
     * @OA\Property(
     *     ref="#/components/schemas/DateTime"
     * )
     */
    public $updated_at;
}
