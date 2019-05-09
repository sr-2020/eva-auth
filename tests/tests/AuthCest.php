<?php

class AuthCest
{
    static protected $createdId = 0;
    static protected $routeAuth = '/login';
    static protected $routeRegister = '/register';

    static protected $data;

    public function registerTest(ApiTester $I)
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
    }

    public function authTest(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(self::$routeAuth, self::$data);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'api_key' => 'string'
        ]);

        $jsonResponse = json_decode($I->grabResponse());
        self::$createdId = $jsonResponse->id;
    }
}
