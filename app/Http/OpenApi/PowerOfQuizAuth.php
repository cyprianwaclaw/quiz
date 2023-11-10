<?php

namespace App\Http\OpenApi;

/**
 * @OA\SecurityScheme(
 *     description="##### To authenticate requests, include an **Authorization** header with the value `Bearer {YOUR_AUTH_KEY}`.
##### All authenticated endpoints are marked with a lock icon in the documentation below.
##### You can retrieve your token by call request to `/api/login`.",
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer"
 * )
 */
class PowerOfQuizAuth
{

}
