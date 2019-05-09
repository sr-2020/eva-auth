<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserRegistration;

/**
 * @OA\Post(
 *     tags={"Auth"},
 *     path="/api/v1/login",
 *     operationId="loginUser",
 *     description="Login as user.",
 *     @OA\RequestBody(
 *         description="Creds for authorization.",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(ref="#/components/schemas/LoginCreds"),
 *             example={"email": "test@email.com", "password": "secret"}
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Authorize user",
 *         @OA\JsonContent(ref="#/components/schemas/UserApiKey")
 *     ),
 *     @OA\Response(
 *         response="default",
 *         description="unexpected error",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
 *     ),
 * )
 */

/**
 * @OA\Post(
 *     tags={"Auth"},
 *     path="/api/v1/register",
 *     operationId="registerUser",
 *     description="Register new user.",
 *     @OA\RequestBody(
 *         description="Creds for registeration.",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(ref="#/components/schemas/RegisterCreds"),
 *             example={"email": "new-user-test@email.com", "password": "secret", "name": "Tim Cook"}
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Register user",
 *         @OA\JsonContent(ref="#/components/schemas/UserApiKey")
 *     ),
 *     @OA\Response(
 *         response="default",
 *         description="unexpected error",
 *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
 *     ),
 * )
 */

class AuthController extends Controller
{
    const MODEL = User::class;

    /**
     * Login method
     *
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->input('email'))->first();
        if (null === $user) {
            return new JsonResponse([], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (!Hash::check($request->input('password'), $user->password)) {
            return new JsonResponse([], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $user->resetApiKey();
        $user->save();

        $user->setVisible(['id', 'api_key']);
        return new JsonResponse($user->toArray(), JsonResponse::HTTP_OK);
    }

    /**
     * Register method
     *
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required',
            'password' => 'required'
        ]);

        try {
            $user = User::create([
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'name' => $request->input('name', '')
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([], JsonResponse::HTTP_BAD_REQUEST);
        }

        Mail::to($user)->send(new UserRegistration($request->all()));

        $user->setVisible(['id', 'api_key']);
        return new JsonResponse($user->toArray(), JsonResponse::HTTP_CREATED);
    }
}
