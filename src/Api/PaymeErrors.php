<?php

namespace PaymeUz\Api;

class PaymeErrors
{
    // General Errors
    public const METHOD_NOT_POST = -32300;
    public const JSON_PARSE_ERROR = -32700;
    public const INVALID_RPC_FIELDS = -32600;
    public const METHOD_NOT_FOUND = -32601;
    public const INSUFFICIENT_PRIVILEGES = -32504;
    public const SYSTEM_ERROR = -32400;

    // Merchant Server Response Errors
    public const INVALID_AMOUNT = -31001;
    public const TRANSACTION_NOT_FOUND = -31003;
    public const TRANSACTION_NOT_CANCELABLE = -31007;
    public const OPERATION_NOT_ALLOWED = -31008;
    public const USER_INPUT_ERROR_MIN = -31050;
    public const USER_INPUT_ERROR_MAX = -31099;

    // CheckPerformTransaction Errors
    public const CHECK_PERFORM_INVALID_AMOUNT = -31001;
    public const CHECK_PERFORM_USER_INPUT_ERROR_MIN = -31050;
    public const CHECK_PERFORM_USER_INPUT_ERROR_MAX = -31099;

    // CreateTransaction Errors
    public const CREATE_TRANSACTION_INVALID_AMOUNT = -31001;
    public const CREATE_TRANSACTION_OPERATION_NOT_ALLOWED = -31008;
    public const CREATE_TRANSACTION_USER_INPUT_ERROR_MIN = -31050;
    public const CREATE_TRANSACTION_USER_INPUT_ERROR_MAX = -31099;

    // PerformTransaction Errors
    public const PERFORM_TRANSACTION_TRANSACTION_NOT_FOUND = -31003;
    public const PERFORM_TRANSACTION_OPERATION_NOT_ALLOWED = -31008;
    public const PERFORM_TRANSACTION_USER_INPUT_ERROR_MIN = -31050;
    public const PERFORM_TRANSACTION_USER_INPUT_ERROR_MAX = -31099;

    // CancelTransaction Errors
    public const CANCEL_TRANSACTION_TRANSACTION_NOT_FOUND = -31003;
    public const CANCEL_TRANSACTION_ALREADY_PERFORMED = -31007;

    // CheckTransaction Errors
    public const CHECK_TRANSACTION_TRANSACTION_NOT_FOUND = -31003;

    // Error messages
    private static array $errorMessages = [
        -31001 => ["ru" => "Неверная сумма заказа", "uz" => "To'lov summasi noto'g'ri", "en" => "Incorrect order amount"],
        -31003 => ["ru" => "Транзакция не найдена", "uz" => "To'lov topilmadi", "en" => "Transaction not found"],
        -31007 => ["ru" => "Невозможно отменить транзакцию! Заказ выполнен.", "uz" => "To'lov bekor qila olmaymiz! Xizmar ko'rsatilgan.", "en" => "Unable to cancel transaction! The order is executed"],
        -31008 => ["ru" => "Невозможно выполнить операцию", "uz" => "So'rovingizni bajarib bo'lmadi. Qayta urinib ko'ring.", "en" => "Unable to perform the operation"],
        -31050 => ["ru" => "Введен неверный номер заказа", "uz" => "Buyurtma raqami noto'g'ri", "en" => "Invalid Order ID"],
        -31051 => ["ru" => "Введен неверный номер заказа 2", "uz" => "Buyurtma raqami noto'g'ri", "en" => "Invalid Order ID"],
        -31052 => ["ru" => "Заказ уже оплачен", "uz" => "Заказ уже оплачен", "en" => "The order has already been paid"],
        -31053 => ["ru" => "В ожидании оплаты", "uz" => "В ожидании оплаты", "en" => "In payment pending"],
        -31054 => ["ru" => "Транзакция отменена", "uz" => "Транзакция отменена", "en" => "Transaction canceled"],
        -31099 => ["ru" => "Запрашиваемый поле не найдено", "uz" => "So'ralgan ma'lumot topilmadi", "en" => "The requested field is not found"],
        -32300 => ["ru" => "Неверный метод запроса", "uz" => "Неверный метод запроса", "en" => "Invalid request method"],
        -32400 => ["ru" => "Не удалось обработать запрос. Повторите попытку еще раз", "uz" => "So'rovingizni bajarib bo'lmadi. Qayta urinib ko'ring.", "en" => "The request could not be processed. Try again."],
        -32504 => ["ru" => "Недостаточно привилегий для выполнения метода", "uz" => "Imtiyozlar yetarli emas", "en" => "Insufficient privileges to execute the method"],
        -32600 => ["ru" => "Отсутствуют обязательные поля в RPC-запросе", "uz" => "Отсутствуют обязательные поля в RPC-запросе", "en" => "Required fields are missing in the RPC request"],
        -32601 => ["ru" => "Запрашиваемый метод не найден", "uz" => "Запрашиваемый метод не найден", "en" => "The requested method was not found"],
        -32700 => ["ru" => "Ошибка парсинга JSON", "uz" => "Ошибка парсинга JSON", "en" => "Error parsing JSON"],
    ];

    /**
     * Get the error message based on the code and language.
     *
     * @param int $code The error code.
     * @param string $lang The language code ("ru", "uz", "en").
     * @return string The error message.
     */
    public static function getErrorMessage(int $code, string $lang = "en"): string
    {
        if (isset(self::$errorMessages[$code])) {
            return self::$errorMessages[$code][$lang] ?? self::$errorMessages[$code]["en"];
        }

        return "Unknown error code.";
    }
}
