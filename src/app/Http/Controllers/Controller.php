<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * @OA\Info(title="EVA Auth API", version="0.1.0", contact={
 *     "email": "gurkalov.dev@gmail.com"
 *   })
 */

/**
 * @OA\Server(url="http://auth.evarun.ru"),
 */

/**
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 *   in="header",
 *   name="Authorization",
 * )
 */

/**
 * @OA\Schema(
 *     schema="ErrorModel",
 *     required={"code", "message"},
 *     @OA\Property(
 *         property="code",
 *         type="integer",
 *         format="int32"
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string"
 *     )
 * )
 */

class Controller extends BaseController
{
    //
}
