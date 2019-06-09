<?php

class ProfileCest
{
    static protected $createdId = 0;
    static protected $createdApiKey = '';
    static protected $route = '/profile';
    static protected $routeRegister = '/register';

    static protected $data;

    public function readTest(ApiTester $I)
    {
        $id = rand(0, 100000);
        $email = 'test' . $id . '@mail.com';
        self::$data = [
            'name' => 'User Test' . $id,
            'email' => $email,
            'password' => 'secret'
        ];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(self::$routeRegister, self::$data);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'api_key' => 'string'
        ]);

        $jsonResponse = json_decode($I->grabResponse());
        self::$createdId = $jsonResponse->id;
        self::$createdApiKey = $jsonResponse->api_key;

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . self::$createdApiKey);
        $I->sendGET(self::$route);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'admin' => 'boolean',
            'status' => 'string',
            'name' => 'string',
            'created_at' => 'string',
            'updated_at' => 'string'
        ]);
    }

    public function updateTest(ApiTester $I)
    {
        $data = [
            'name' => 'New Name' . rand(0, 100000),
            'status' => 'NewStatus',
            'options' => [
                'a' => true,
                'b' => 1,
                'c' => 'str',
                'd' => [
                    'a' => true
                ],
                'e' => null
            ]
        ];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . self::$createdApiKey);
        $I->sendPUT(self::$route, $data);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'admin' => 'boolean',
            'status' => 'string',
            'name' => 'string',
            'created_at' => 'string',
            'updated_at' => 'string',
            'options' => 'array',
        ]);

        $I->canSeeResponseContainsJson([
            'name' => $data['name'],
            'status' => $data['status'],
            'options' => $data['options']
        ]);
    }
}
