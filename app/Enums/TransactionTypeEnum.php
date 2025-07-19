<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case PURCHASE = 'purchase';
    case REDEMPTION = 'redemption';
    case EARN = 'earn';
    case TRANSFER_OUT = 'transfer_out';
    case TRANSFER_IN = 'transfer_in';
    case BONUS = 'bonus';
    case PURCHASE_REWARD = 'purchase_reward';
}