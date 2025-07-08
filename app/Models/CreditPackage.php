<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditPackage extends Model
{
    protected $fillable = ['name', 'credit_amount', 'price', 'bonus_points', 'is_active'];

    public function creditPackage(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
}
