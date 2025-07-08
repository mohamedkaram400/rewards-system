<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case PURCHASE = 'purchase';
    case REDEMPTION = 'redemption';
}