<?php

class UserCest
{
    static protected $createdId = 0;
    static protected $route = '/users';

    public function indexTest(ApiTester $I)
    {
        $I->sendGET(self::$route);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->canSeeResponseContainsJson([]);
    }

    public function createTest(ApiTester $I)
    {
        $email = 'test' . rand(0, 10000) . '@mail.com';
        $data = [
            'name' => 'User Test',
            'email' => $email,
            'password' => 'secret'
        ];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Authorization', 'Token ' . $I->getAdminToken());
        $I->sendPOST(self::$route, $data);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'name' => 'string'
        ]);
        $I->canSeeResponseContainsJson([
            'name' => $data['name']
        ]);

        $jsonResponse = json_decode($I->grabResponse());
        self::$createdId = $jsonResponse->id;
    }
    
    public function readTest(ApiTester $I)
    {
        $I->sendGET(self::$route . '/' . self::$createdId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'name' => 'string'
        ]);
        $I->canSeeResponseContainsJson([
            'id' => self::$createdId
        ]);
    }

    public function updateTest(ApiTester $I)
    {
        $name = 'User Test New';
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Authorization', 'Token ' . $I->getAdminToken());
        $I->sendPUT(self::$route . '/'  . self::$createdId, ['name' => $name]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'name' => 'string'
        ]);
        $I->canSeeResponseContainsJson([
            'id' => self::$createdId,
            'name' => $name,
        ]);
    }    

    public function deleteTest(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Token ' . $I->getAdminToken());
        $I->sendDELETE(self::$route . '/' . self::$createdId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NO_CONTENT);
    }
    
    public function tryReadTest(ApiTester $I)
    {
        $I->sendGET(self::$route . '/' . self::$createdId);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND);
    }
}
