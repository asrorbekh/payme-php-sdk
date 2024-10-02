<?php

declare(strict_types=1);

namespace PaymeUz\HttpClient;

use Curl\Curl;

class SubscribeApiHttpClient extends BaseApiHttpClient
{
    /**
     * Constructor to initialize the Subscribe API HTTP client
     *
     * @param string|null $id The cash register ID
     * @param string|null $key The secret key for the merchant
     * @param string|null $apiUrl The base URL of the API
     * @param bool|null $isTest Flag to indicate if test mode is active
     */
    public function __construct(
        private readonly string|null $id = null,
        private readonly string|null $key = null,
        private string|null $apiUrl = null,
        private readonly bool|null $isTest = false,
    ) {
        if (!$this->apiUrl) {
            $this->apiUrl = $this->isTest ? self::BASE_TEST_URL : self::BASE_URL;
        }
    }

    /**
     * Send the HTTP request with a dynamic method
     *
     * @param string $method The API method to call (e.g., cards.create, cards.verify)
     * @param array $params The parameters to send in the request
     * @param string $httpMethod The HTTP method (e.g., POST, GET, PUT, DELETE)
     * @return object The response from the API
     */
    public function sendRequest(string $method, array $params = [], string $httpMethod = 'POST'): object
    {
        $curl = new Curl();
        $curl->setTimeout($this->timeout);

        if ($this->key) {
            $token = "{$this->id}:{$this->key}";
        } else {
            $token = "$this->id";
        }

        $curl->setHeader('X-Auth', $token);

        $curl->setHeader('Content-Type', 'application/json');

        $postFields = [
            'method' => $method,
            'params' => $params,
            'id' => time(), // Unique ID for the request
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
}
