<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    protected $fillable = ['user_id', 'status', 'price_paid', 'credit_package_id', 'transaction_id'];

    public function creditPackage(): BelongsTo
    {
        return $this->belongsTo(CreditPackage::class);
    }
}
