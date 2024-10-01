<?php

namespace PaymeUz\HttpClient;

use Curl\Curl;

class TelegramBotApiHttpClient extends BaseApiHttpClient
{
    protected string $telegramBaseUrl = 'https://api.telegram.org/bot';

    public function __construct(private readonly string $botToken)
    {
        $this->telegramBaseUrl .= $this->botToken;
    }

    /**
     * Send a message to the user using the Telegram Bot API.
     *
     * @param array $params Params
     * @return object The response from the API.
     */
    public function sendMessage(array $params): object
    {
        return $this->sendRequest('sendMessage', $params);
    }

    /**
     * Send an invoice to the user using the Telegram Bot API.
     *
     * @param array $params Parameters required by the sendInvoice method.
     * @return object The response from the API.
     */
    public function sendInvoice(array $params): object
    {
        return $this->sendRequest('sendInvoice', $params);
    }

    /**
     * A helper method to send HTTP requests using cURL.
     *
     * @param string $method The API method to call.
     * @param array $params The parameters to send in the request.
     * @return object The response from the API.
     */
    protected function sendRequest(string $method, array $params = []): object
    {
        $curl = new Curl();
        $curl->setTimeout($this->timeout);
        $curl->setHeader('Content-Type', 'application/json');

        $url = $this->telegramBaseUrl . '/' . $method;

        $curl->post($url, json_encode($params));

        if ($curl->error) {
            $this->response = new \stdClass();
            $this->response->status = false;
            $this->response->error = $curl->errorMessage;
            $this->response->code = $curl->errorCode;
        } else {
            $this->response = $curl->response;
        }

        return $this->response;
    }
}
