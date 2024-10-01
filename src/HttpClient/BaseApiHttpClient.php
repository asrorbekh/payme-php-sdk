<?php

namespace PaymeUz\HttpClient;

abstract class BaseApiHttpClient
{
    protected const BASE_TEST_URL = 'https://checkout.test.paycom.uz/api';
    protected const BASE_URL = 'https://checkout.paycom.uz/api';
    protected int $timeout = 30;
    public object|null $response = null;

}
