<?php

namespace PaymeUz\Exception;

use Exception;
use Throwable;

class PaymeException extends Exception implements Throwable
{
    public function __construct(string $message = "", int $code = 0, Throwable|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }


}