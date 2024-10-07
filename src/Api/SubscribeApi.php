<?php

namespace PaymeUz\Api;

use PaymeUz\HttpClient\SubscribeApiHttpClient;

class SubscribeApi extends BaseApi
{
    private SubscribeApiHttpClient $client;

    /**
     * SubscribeApi constructor.
     *
     * @param string|null $id The ID of the cash register (merchant ID).
     * @param string|null $key The API key for the merchant.
     * @param bool $isTest Flag to determine if using the test environment.
     */
    public function __construct(
        private readonly string|null $id = null,
        private readonly string|null $key = null,
        private readonly bool $isTest = false
    ) {
        $this->client = new SubscribeApiHttpClient(
            id: $this->id, key: $this->key, isTest: $this->isTest
        );
    }

    /**
     * Create a plastic card token.
     *
     * @param string $cardNumber The card number to tokenize.
     * @param string $expiryDate Card expiration date in format MM/YY.
     * @param bool $save Whether to save the token for future payments.
     * @param string|null $customer Optional user ID (phone, uid, email).
     * @param array|null $account Optional account information.
     * @return object
     */
    public function createCard(
        string $cardNumber,
        string $expiryDate,
        bool $save = false,
        string|null $customer = null,
        array|null $account = null
    ): object {
        $params = [
            'card' => [
                'number' => $cardNumber,
                'expire' => $expiryDate
            ],
            'save' => $save
        ];

        if ($customer) {
            $params['customer'] = $customer;
        }

        if ($account) {
            $params['account'] = $account;
        }

        return $this->client->sendRequest(SubscribeApiMethods::CARDS_CREATE, $params);
    }

    /**
     * Request a verification code for a card token.
     *
     * @param string $token The token of the card for which to request the verification code.
     * @return object
     */
    public function getVerifyCode(string $token): object
    {
        $params = [
            'token' => $token
        ];

        return $this->client->sendRequest(SubscribeApiMethods::CARDS_GET_VERIFY_CODE, $params);
    }

    /**
     * Verify a card using the token and OTP code.
     *
     * @param string $token The card token to verify.
     * @param string $code The OTP verification code sent via SMS.
     * @return object
     */
    public function verifyCard(string $token, string $code): object
    {
        $params = [
            'token' => $token,
            'code' => $code
        ];

        return $this->client->sendRequest(SubscribeApiMethods::CARDS_VERIFY, $params);
    }

    /**
     * Check the status of a card token.
     *
     * @param string $token The card token to check.
     * @return object
     */
    public function checkCard(string $token): object
    {
        $params = [
            'token' => $token
        ];

        return $this->client->sendRequest(SubscribeApiMethods::CARDS_CHECK, $params);
    }

    /**
     * Remove a card token.
     *
     * @param string $token The card token to remove.
     * @return object
     */
    public function removeCard(string $token): object
    {
        $params = [
            'token' => $token
        ];

        return $this->client->sendRequest(SubscribeApiMethods::CARDS_REMOVE, $params);
    }

    /**
     * Create a receipt for payment.
     *
     * @param int $amount The amount to be paid (in tiyins).
     * @param array $account The account details (e.g., order ID).
     * @param string|null $description Optional payment description.
     * @param array|null $detail Optional payment detail object (shipping, items).
     *
     * @return object
     */
    public function createReceipt(
        int $amount,
        array $account,
        string|null $description = null,
        array|null $detail = null
    ): object {
        $params = [
            'amount' => $amount,
            'account' => $account
        ];

        if ($description) {
            $params['description'] = $description;
        }

        if ($detail) {
            $params['detail'] = $detail;
        }

        return $this->client->sendRequest(SubscribeApiMethods::RECEIPTS_CREATE, $params);
    }

    /**
     * Pay a receipt using a card token.
     *
     * @param string $receiptId The receipt ID to be paid.
     * @param string $token The card token for the payment.
     * @param array|null $payer Optional payer information.
     *
     * @return object
     */
    public function payReceipt(string $receiptId, string $token, array $payer = null): object
    {
        $params = [
            'id' => $receiptId,
            'token' => $token
        ];

        if ($payer) {
            $params['payer'] = $payer;
        }

        return $this->client->sendRequest(SubscribeApiMethods::RECEIPTS_PAY, $params);
    }

    /**
     * Send a payment receipt via SMS.
     *
     * @param string $receiptId The receipt ID to be sent.
     * @param string $phone The recipient's phone number.
     *
     * @return object
     */
    public function sendReceipt(string $receiptId, string $phone): object
    {
        $params = [
            'id' => $receiptId,
            'phone' => $phone
        ];

        return $this->client->sendRequest(SubscribeApiMethods::RECEIPTS_SEND, $params);
    }

    /**
     * Cancel a receipt.
     *
     * @param string $receiptId The receipt ID to be canceled.
     *
     * @return object
     */
    public function cancelReceipt(string $receiptId): object
    {
        $params = [
            'id' => $receiptId
        ];

        return $this->client->sendRequest(SubscribeApiMethods::RECEIPTS_CANCEL, $params);
    }

    /**
     * Check the status of a receipt.
     *
     * @param string $receiptId The receipt ID to check the status.
     *
     * @return object
     */
    public function checkReceipt(string $receiptId): object
    {
        $params = [
            'id' => $receiptId
        ];

        return $this->client->sendRequest(SubscribeApiMethods::RECEIPTS_CHECK, $params);
    }


    /**
     * Get full information of a receipt.
     *
     * @param string $receiptId The receipt ID to retrieve the details.
     *
     * @return object Receipt details.
     */
    public function getReceipt(string $receiptId): object
    {
        $params = [
            'id' => $receiptId
        ];

        return $this->client->sendRequest(SubscribeApiMethods::RECEIPTS_GET, $params);
    }

    /**
     * Get all receipts within a specified date range.
     *
     * @param int $count The number of receipts to retrieve (max 50).
     * @param int $from Start date timestamp.
     * @param int $to End date timestamp.
     * @param int $offset Number of receipts to skip (optional).
     *
     * @return object List of receipts.
     */
    public function getAllReceipts(int $count, int $from, int $to, int $offset = 0): object
    {
        $params = [
            'count' => $count,
            'from' => $from,
            'to' => $to,
            'offset' => $offset
        ];

        return $this->client->sendRequest(SubscribeApiMethods::RECEIPTS_GET_ALL, $params);
    }

    /**
     * Set fiscal data for a receipt.
     *
     * @param string $receiptId The unique check ID in Payme DB.
     * @param array $fiscalData The fiscal data to be transferred.
     *
     * @return object
     */
    public function setFiscalData(string $receiptId, array $fiscalData): object
    {
        $params = [
            'id' => $receiptId,
            'fiscal_data' => $fiscalData
        ];

        return $this->client->sendRequest(SubscribeApiMethods::RECEIPTS_SET_FISCAL_DATA, $params);
    }

    /**
     * Confirm hold for a receipt.
     *
     * @param string $receiptId The unique check ID in Payme DB.
     * @return object
     */
    public function confirmHold(string $receiptId): object
    {
        $params = [
            'id' => $receiptId
        ];

        return $this->client->sendRequest(SubscribeApiMethods::RECEIPTS_CONFIRM_HOLD, $params);
    }
}
