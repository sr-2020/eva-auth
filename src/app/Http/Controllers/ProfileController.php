<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     tags={"Profile"},
 *     path="/api/v1/profile",
 *     description="Returns a user info which auth for token",
 *     operationId="profileUser",
 *     @OA\Response(
 *         response=200,
 *         description="User response",
 *         @OA\JsonContent(ref="#/components/schemas/User"),
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="unexpected error",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */

/**
 * @OA\Put(
 *     tags={"Profile"},
 *     path="/api/v1/profile",
 *     operationId="updateProfile",
 *     description="Update user profile.",
 *     @OA\RequestBody(
 *         description="Creds for registeration.",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(ref="#/components/schemas/User"),
 *             example={"email": "api-test@email.com", "password": "secret", "name": "Api Tim Cook", "status": "free"}
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Register user",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="unexpected error",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */

class ProfileController extends Controller
{
    const MODEL = User::class;

    /**
     * Get user auth info
     *
     * @return JsonResponse
     */
    public function read(Request $request)
    {
        $user = $request->user();
        $model= User::findOrFail($user->id);
        return new JsonResponse($model, JsonResponse::HTTP_OK);
    }

    /**
     * Update user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $model= User::findOrFail($user->id);
        $model->fill($request->all());
        $model->save();
        return new JsonResponse($model, JsonResponse::HTTP_OK);
    }
}
