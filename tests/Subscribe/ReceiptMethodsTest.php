<?php

namespace Subscribe;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PaymeUz\HttpClient\SubscribeApiHttpClient;
use PaymeUz\Api\SubscribeApi;
use ReflectionClass;

class ReceiptMethodsTest extends TestCase
{
    private SubscribeApi $api;
    private SubscribeApiHttpClient|MockObject $clientMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        // Create a mock for the SubscribeApiHttpClient
        $this->clientMock = $this->createMock(SubscribeApiHttpClient::class);

        // Inject the mock client into the SubscribeApi instance
        $this->api = new SubscribeApi(
            id: 'test_merchant_id',
            key: 'test_api_key',
            isTest: true
        );

        // Override the private client property with the mock
        $reflection = new ReflectionClass($this->api);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setValue($this->api, $this->clientMock);
    }

    public function testCreateCard(): void
    {
        $cardNumber = '8600069195406311';
        $expiryDate = '03/99';
        $save = true;

        // Define the expected response
        $expectedResponse = (object)[
            'status' => 'success',
            'body' => (object)[
                'result' => (object)[
                    'card' => (object)[
                        'token' => 'mocked_token'
                    ]
                ]
            ]
        ];

        // Set the expectation for the client mock
        $this->clientMock->expects($this->once())
            ->method('sendRequest')
            ->with('cards.create', [
                'card' => [
                    'number' => $cardNumber,
                    'expire' => $expiryDate
                ],
                'save' => $save
            ])
            ->willReturn($expectedResponse);

        // Call the method and assert the result
        $result = $this->api->createCard($cardNumber, $expiryDate, $save);
        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateReceipt(): void
    {
        $amount = 500000; // unit amount 5000.00 SUM
        $orderId = 443;
        $account = ['order_id' => $orderId];
        $description = "Payment for order #$orderId";
        $productItem = [
            'discount' => 0,
            'title' => 'Tomatoes',
            'price' => 505000,
            'count' => 2,
            'code' => '00702001001000001',
            'units' => 241092,
            'vat_percent' => 15,
            'package_code' => '123456'
        ];
        $detail = [
            'receipt_type' => 0,
            'items' => [$productItem]
        ];

        // Define the expected response
        $expectedResponse = (object)[
            'status' => 'success',
            'body' => (object)[
                'result' => (object)[
                    'receipt_id' => 'mocked_receipt_id'
                ]
            ]
        ];

        // Set the expectation for the client mock
        $this->clientMock->expects($this->once())
            ->method('sendRequest')
            ->with('receipts.create', [
                'amount' => $amount,
                'account' => $account,
                'description' => $description,
                'detail' => $detail
            ])
            ->willReturn($expectedResponse);

        // Call the method and assert the result
        $result = $this->api->createReceipt($amount, $account, $description, $detail);
        $this->assertEquals($expectedResponse, $result);
    }

    public function testPayReceipt(): void
    {
        $receiptId = 'mocked_receipt_id';
        $token = 'mocked_token';
        $payer = ['payer_id' => 'test_payer'];

        // Define the expected response
        $expectedResponse = (object)[
            'status' => 'success',
            'body' => (object)[
                'result' => (object)[
                    'payment_id' => 'mocked_payment_id'
                ]
            ]
        ];

        // Set the expectation for the client mock
        $this->clientMock->expects($this->once())
            ->method('sendRequest')
            ->with('receipts.pay', [
                'id' => $receiptId,
                'token' => $token,
                'payer' => $payer
            ])
            ->willReturn($expectedResponse);

        // Call the method and assert the result
        $result = $this->api->payReceipt($receiptId, $token, $payer);
        $this->assertEquals($expectedResponse, $result);
    }

    public function testCancelReceipt(): void
    {
        $receiptId = 'mocked_receipt_id';

        // Define the expected response
        $expectedResponse = (object)[
            'status' => 'success'
        ];

        // Set the expectation for the client mock
        $this->clientMock->expects($this->once())
            ->method('sendRequest')
            ->with('receipts.cancel', ['id' => $receiptId])
            ->willReturn($expectedResponse);

        // Call the method and assert the result
        $result = $this->api->cancelReceipt($receiptId);
        $this->assertEquals($expectedResponse, $result);
    }
}
