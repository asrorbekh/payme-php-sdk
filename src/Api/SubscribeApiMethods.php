<?php

namespace PaymeUz\Api;

class SubscribeApiMethods
{
    // Client-side card methods
    public const CARDS_CREATE = 'cards.create';
    public const CARDS_GET_VERIFY_CODE = 'cards.get_verify_code';
    public const CARDS_VERIFY = 'cards.verify';

    // Server-side card methods
    public const CARDS_CHECK = 'cards.check';
    public const CARDS_REMOVE = 'cards.remove';

    // Server-side receipt methods
    public const RECEIPTS_CREATE = 'receipts.create';
    public const RECEIPTS_PAY = 'receipts.pay';
    public const RECEIPTS_SEND = 'receipts.send';
    public const RECEIPTS_CANCEL = 'receipts.cancel';
    public const RECEIPTS_CHECK = 'receipts.check';
    public const RECEIPTS_GET = 'receipts.get';
    public const RECEIPTS_GET_ALL = 'receipts.get_all';
    public const RECEIPTS_SET_FISCAL_DATA = 'receipts.set_fiscal_data';
}
