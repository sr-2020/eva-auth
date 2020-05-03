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
    public function testProfileBearerAuthorizationSuccess()
    {
        $user = factory(App\User::class)->make();
        $user->save();

        $this->json('GET', '/api/v1/profile', $user->toArray(), [
            'Authorization' => 'Bearer ' . $user->api_key
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
}
