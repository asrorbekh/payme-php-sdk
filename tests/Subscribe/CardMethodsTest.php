<?php

namespace Subscribe;

use PHPUnit\Framework\TestCase;

use PaymeUz\Api\SubscribeApi;

class CardMethodsTest extends TestCase
{
    protected SubscribeApi $api;

    protected function setUp(): void
    {
        $this->api = $this->getMockBuilder(SubscribeApi::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['createCard', 'getVerifyCode', 'verifyCard', 'checkCard', 'removeCard'])
            ->getMock();
    }

    public function testCreateCard()
    {
        $cardNumber = '8600069195406311';
        $expiryDate = '03/99';
        $isTest = true;

        $this->api->method('createCard')
            ->willReturn((object)[
                'status' => true,
                'body' => (object)[
                    'result' => (object)[
                        'card' => (object)[
                            'token' => 'mock_token'
                        ]
                    ]
                ]
            ]);

        $result = $this->api->createCard($cardNumber, $expiryDate, $isTest);
        $this->assertTrue($result->status);
        $this->assertEquals('mock_token', $result->body->result->card->token);
    }

    public function testVerifyCard()
    {
        $token = 'mock_token';
        $otpCode = '666666';

        $this->api->method('verifyCard')
            ->willReturn((object)[
                'status' => true,
                'body' => (object)[
                    'result' => 'verified'
                ]
            ]);

        $result = $this->api->verifyCard($token, $otpCode);
        $this->assertTrue($result->status);
        $this->assertEquals('verified', $result->body->result);
    }

    public function testRemoveCard()
    {
        $token = 'mock_token';

        $this->api->method('removeCard')
            ->willReturn((object)[
                'status' => true,
                'body' => (object)[
                    'result' => 'removed'
                ]
            ]);

        $result = $this->api->removeCard($token);
        $this->assertTrue($result->status);
        $this->assertEquals('removed', $result->body->result);
    }
}
