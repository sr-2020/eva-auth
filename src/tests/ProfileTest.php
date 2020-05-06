<?php

use App\Http\Controllers\UserController;
use App\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Http\JsonResponse;

class ProfileTest extends TestCase
{
    use DatabaseMigrations;

    const CONTROLLER = UserController::class;

    /**
     * Create factory.
     *
     * @return \Laravel\Lumen\Application
     */
    protected function makeFactory()
    {
        $controller = static::CONTROLLER;
        $model = factory($controller::MODEL)->make();
        return $model;
    }

    /**
     * A basic test create.
     *
     * @return void
     */
    public function testProfileAuthorizationSuccess()
    {
        $user = factory(App\User::class)->make();
        $user->save();

        $this->json('GET', '/api/v1/profile', $user->toArray(), [
            'Authorization' => $user->api_key
        ])
            ->seeStatusCode(JsonResponse::HTTP_OK)
            ->seeJsonEquals($user->toArray());
    }

    /**
     * A basic test create.
     *
     * @return void
     */
    public function testProfileAuthorizationByXUserIdSuccess()
    {
        $user = factory(App\User::class)->make();
        $user->save();

        $this->json('GET', '/api/v1/profile', [], [
            'X-User-Id' => $user->id
        ])
            ->seeStatusCode(JsonResponse::HTTP_OK)
            ->seeJsonEquals($user->toArray());
    }

    /**
     * A basic test create.
     *
     * @return void
     */
    public function testProfileAuthorizationFail()
    {
        $user = factory(App\User::class)->make();
        $user->save();

        $this->json('GET', '/api/v1/profile', $user->toArray(), [
            'Authorization' => 'Bearer test'
        ])
            ->seeStatusCode(JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * A basic test create.
     *
     * @return void
     */
    public function testProfileAuthorizationByXUserIdFail()
    {
        $user = factory(App\User::class)->make();
        $user->save();

        $this->json('GET', '/api/v1/profile', [], [
            'X-User-Id' => ''
        ])
            ->seeStatusCode(JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * A basic test create.
     *
     * @return void
     */
    public function testUpdateProfileAuthorizationSuccess()
    {
        $user = factory(App\User::class)->make();
        $user->save();

        $this->json('PUT', '/api/v1/profile', $user->toArray(), [
            'Authorization' => $user->api_key
        ])
            ->seeStatusCode(JsonResponse::HTTP_OK)
            ->seeJson($user->toArray());
    }

    /**
     * A basic test create.
     *
     * @return void
     */
    public function testUpdateProfileTryUpdateSensitiveSuccess()
    {
        $user = factory(App\User::class)->make();
        $user->save();

        $data = [
            'admin' => true,
            'api_key' => 'UnFQb2RqWVF4SXdrMWQ1NVNRNUQySURl',
        ];

        $this->json('PUT', '/api/v1/profile', $data, [
            'Authorization' => $user->api_key
        ])
            ->seeStatusCode(JsonResponse::HTTP_OK)
            ->seeJson($user->toArray())
            ->seeJsonContains([
                'admin' => false,
            ]);
    }

    /**
     * A basic test create.
     *
     * @return void
     */
    public function testUpdateProfileAuthorizationFail()
    {
        $user = factory(App\User::class)->make();
        $user->save();

        $this->json('PUT', '/api/v1/profile', $user->toArray(), [
            'Authorization' => 'Bearer test'
        ])
            ->seeStatusCode(JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * A basic test create.
     *
     * @return void
     */
    public function testUpdateProfileTryChangeEmailToAlreaseyExistsEmail()
    {
        $userFirst = factory(App\User::class)->make();
        $userFirst->save();

        $userSecond = factory(App\User::class)->make();
        $userSecond->save();

        $data = [
            'email' => $userSecond->email,
        ];

        $this->json('PUT', '/api/v1/profile', $data, [
            'Authorization' => $userFirst->api_key
        ])
            ->seeStatusCode(JsonResponse::HTTP_BAD_REQUEST)
            ->seeJson(['error' => 'UNIQUE constraint failed: users.email']);
    }
}
