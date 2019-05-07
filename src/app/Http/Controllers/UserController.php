<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Get(
 *     tags={"User"},
 *     path="/api/v1/users",
 *     description="Returns all users",
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         description="maximum number of results to return",
 *         required=false,
 *         example="10",
 *         @OA\Schema(
 *             type="integer",
 *             format="int32"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User response",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/User")
 *         ),
 *     ),
 * )
 */

/**
 * @OA\Get(
 *     tags={"User"},
 *     path="/api/v1/users/{id}",
 *     description="Returns a user based on a single ID",
 *     operationId="getUser",
 *     @OA\Parameter(
 *         description="ID of user to fetch",
 *         in="path",
 *         name="id",
 *         required=true,
 *         example=1,
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User response",
 *         @OA\JsonContent(ref="#/components/schemas/User"),
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="unexpected error",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
 *     )
 * )
 */

/**
 * #OA\Post(
 *     tags={"User"},
 *     path="/api/v1/users",
 *     operationId="createUser",
 *     description="Creates a new user.",
 *     #OA\RequestBody(
 *         description="User to add.",
 *         required=true,
 *         #OA\MediaType(
 *             mediaType="application/json",
 *             #OA\Schema(ref="#/components/schemas/NewUser")
 *         )
 *     ),
 *     #OA\Response(
 *         response=200,
 *         description="User response",
 *         #OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     #OA\Response(
 *         response="default",
 *         description="unexpected error",
 *         #OA\JsonContent(ref="#/components/schemas/ErrorModel"),
 *     ),
 * )
 */

/**
 * #OA\Put(
 *     tags={"User"},
 *     path="/api/v1/users/{id}",
 *     description="Update a user based on a single ID.",
 *     operationId="updateUser",
 *     #OA\Parameter(
 *         description="ID of user to fetch",
 *         in="path",
 *         name="id",
 *         required=true,
 *         #OA\Schema(
 *             type="integer",
 *             format="int64",
 *         )
 *     ),
 *     #OA\RequestBody(
 *         description="User to update.",
 *         required=true,
 *         #OA\MediaType(
 *             mediaType="application/json",
 *             #OA\Schema(ref="#/components/schemas/NewUser")
 *         )
 *     ),
 *     #OA\Response(
 *         response=200,
 *         description="User response",
 *         #OA\JsonContent(ref="#/components/schemas/User"),
 *     ),
 *     #OA\Response(
 *         response="default",
 *         description="unexpected error",
 *         #OA\JsonContent(ref="#/components/schemas/ErrorModel"),
 *     )
 * )
 */

/**
 * #OA\Delete(
 *     tags={"User"},
 *     path="/api/v1/users/{id}",
 *     description="Deletes a single user based on the ID.",
 *     operationId="deleteUser",
 *     #OA\Parameter(
 *         description="ID of user to delete",
 *         in="path",
 *         name="id",
 *         required=true,
 *         #OA\Schema(
 *             format="int64",
 *             type="integer"
 *         )
 *     ),
 *     #OA\Response(
 *         response=204,
 *         description="User deleted"
 *     ),
 *     #OA\Response(
 *         response="default",
 *         description="unexpected error",
 *         #OA\JsonContent(ref="#/components/schemas/ErrorModel"),
 *     )
 * )
 */
class UserController extends Controller
{
    use Traits\CrudTrait;
    
    const MODEL = User::class;
}
