<?php

namespace PaymeUz\Api;

use PaymeUz\HttpClient\MerchantApiHttpClient;

class MerchantApi extends BaseApi
{
    private MerchantApiHttpClient $client;

    /**
     * MerchantApi constructor
     *
     * @param string $login Merchant API login -> use Paycom
     * @param string $password Merchant API password
     * @param bool $isTest Flag to determine if using test API
     */
    public function __construct(
        private readonly string $login,
        private readonly string $password,
        private readonly bool $isTest = false
    ) {
        $this->client = new MerchantApiHttpClient(
            login: $this->login, password: $this->password, isTest: $this->isTest
        );
    }

    /**
     * Check if a transaction can be performed
     *
     * @param int $amount Amount of transaction in tiyins (smallest unit)
     * @param array $account Account information for the transaction
     * @return object         API response object
     */
    public function checkPerformTransaction(int $amount, array $account): object
    {
        $params = [
            'amount' => $amount,
            'account' => $account
        ];

        return $this->client->sendRequest(MerchantApiMethods::CHECK_PERFORM_TRANSACTION, $params);
    }

    /**
     * Create a transaction
     *
     * @param string $id Transaction ID
     * @param int $time Time of transaction creation (timestamp)
     * @param int $amount Amount of transaction in tiyins
     * @param array $account Account information for the transaction
     * @return object          API response object
     */
    public function createTransaction(string $id, int $time, int $amount, array $account): object
    {
        $params = [
            'id' => $id,
            'time' => $time,
            'amount' => $amount,
            'account' => $account
        ];

        return $this->client->sendRequest(MerchantApiMethods::CREATE_TRANSACTION, $params);
    }

    /**
     * Perform a transaction (complete it)
     *
     * @param string $id Transaction ID
     * @return object          API response object
     */
    public function performTransaction(string $id): object
    {
        $params = [
            'id' => $id
        ];

        return $this->client->sendRequest(MerchantApiMethods::PERFORM_TRANSACTION, $params);
    }

    /**
     * Cancel a transaction
     *
     * @param string $id Transaction ID
     * @param int $reason Reason for cancellation (1 for customer cancellation, 2 for other reasons)
     * @return object          API response object
     */
    public function cancelTransaction(string $id, int $reason): object
    {
        $params = [
            'id' => $id,
            'reason' => $reason
        ];

        return $this->client->sendRequest(MerchantApiMethods::CANCEL_TRANSACTION, $params);
    }

    /**
     * Check the status of a transaction
     *
     * @param string $id Transaction ID
     * @return object          API response object
     */
    public function checkTransaction(string $id): object
    {
        $params = [
            'id' => $id
        ];

        return $this->client->sendRequest(MerchantApiMethods::CHECK_TRANSACTION, $params);
    }

    /**
     * Get a list of transactions for a given period
     *
     * @param int $from Start timestamp of the period
     * @param int $to End timestamp of the period
     * @return object          API response object
     */
    public function getStatement(int $from, int $to): object
    {
        $params = [
            'from' => $from,
            'to' => $to
        ];

        return $this->client->sendRequest(MerchantApiMethods::GET_STATEMENT, $params);
    }

    /**
     * Set fiscal data for a payment or cancellation receipt
     *
     * @param string $id Unique check id in Payme DB
     * @param string $type Check type ("PERFORM" for payment or "CANCEL" for cancellation)
     * @param array $fiscalData Data on receipt fiscalization
     * @return object           API response object
     */
    public function setFiscalData(string $id, string $type, array $fiscalData): object
    {
        $params = [
            'id' => $id,
            'type' => $type,
            'fiscal_data' => $fiscalData
        ];

        return $this->client->sendRequest(MerchantApiMethods::SET_FISCAL_DATA, $params);
    }
}
