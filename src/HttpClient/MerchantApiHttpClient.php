<?php

namespace PaymeUz\HttpClient;

use Curl\Curl;

/**
 * Class MerchantApiHttpClient
 *
 * Handles HTTP communication for Merchant API using php-curl-class
 */
class MerchantApiHttpClient extends BaseApiHttpClient
{
    /**
     * @param string $login Merchant API login credential
     * @param string $password Merchant API password credential
     * @param string|null $apiUrl API URL
     * @param bool|null $isTest Whether to use test URL or production URL
     */
    public function __construct(
        private readonly string $login,
        private readonly string $password,
        protected string|null $apiUrl = null,
        private readonly bool|null $isTest = false,
    ) {
        if (!$this->apiUrl) {
            $this->apiUrl = $this->isTest ? self::BASE_TEST_URL : self::BASE_URL;
        }
    }

    /**
     * Send the HTTP request dynamically using different methods
     *
     * @param string $method The API method to call (CheckPerformTransaction, etc.)
     * @param array $params The parameters to send in the request
     * @param string $httpMethod HTTP method (GET, POST, PUT, DELETE)
     *
     * @return object             The response from the API
     */
    public function sendRequest(string $method, array $params = [], string $httpMethod = 'POST'): object
    {
        $curl = new Curl();
        $curl->setTimeout($this->timeout);

        $curl->setBasicAuthentication($this->login, $this->password);

        $curl->setHeader('Content-Type', 'application/json');

        $postFields = [
            'method' => $method,
            'params' => $params,
            'id' => time(), // Use a unique request ID based on the current time
        ];

        switch (strtoupper($httpMethod)) {
            case 'GET':
                $curl->get($this->apiUrl, $params);
                break;
            case 'PUT':
                $curl->put($this->apiUrl, json_encode($postFields));
                break;
            case 'DELETE':
                $curl->delete($this->apiUrl, json_encode($postFields));
                break;
            case 'PATCH':
                $curl->patch($this->apiUrl, json_encode($postFields));
                break;
            case 'POST':
            default:
                $curl->post($this->apiUrl, json_encode($postFields));
                break;
        }

        $this->response = new \stdClass();
        $this->response->method = $method;
        $this->response->params = $params;
        $this->response->httpMethod = $httpMethod;

        // Handle error or success responses
        if ($curl->error) {
            $this->response->status = false;

            $this->response->httpError = $curl->httpError;
            $this->response->httpErrorMessage = $curl->httpErrorMessage;
            $this->response->httpStatusCode = $curl->httpStatusCode;

            $this->response->errorMessage = $curl->errorMessage;
            $this->response->errorCode = $curl->errorCode;
        } elseif (isset($curl->response->error) && $curl->response->error) {
            $this->response->status = false;

            $this->response->httpError = $curl->httpError;
            $this->response->httpErrorMessage = $curl->httpErrorMessage;
            $this->response->httpStatusCode = $curl->httpStatusCode;

            $this->response->error = $curl->response->error;
        } else {
            $this->response->status = true;
        }

        $this->response->body = $curl->response;

        return $this->response;
    }

    public function __toString(): string
    {
        return json_encode($this->response);
    }
}
