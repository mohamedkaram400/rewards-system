<?php
namespace App\Enums;

enum TransactionSourceEnum: string
{
    case REDEEMED_PRODUCT = 'redeemed_product';
    case ORDER = 'order';
    case PROMOTION = 'promotion';
    case MANUAL_ADJUSTMENT = 'manual_adjustment';
    case TRANSFER_TO_USER = 'transfer_to_user';
    case TRANSFER_FROM_USER = 'transfer_from_user';
    case ADMIN_BONUS = 'admin_bonus';

    public function label(?int $relatedId = null, ?string $extra = null): string
    {
        return match ($this) {
            self::REDEEMED_PRODUCT     => "Redeemed: Product #{$relatedId}",
            self::ORDER                => "Earned from Order #{$relatedId}",
            self::PROMOTION            => "Promotion: {$extra}",
            self::MANUAL_ADJUSTMENT   => "Manual Admin Adjustment",
            self::TRANSFER_TO_USER     => "Transfer to User #{$relatedId}",
            self::TRANSFER_FROM_USER   => "Transfer from User #{$relatedId}",
            self::ADMIN_BONUS         => "Admin Bonus: {$extra}",
        };
    }
}