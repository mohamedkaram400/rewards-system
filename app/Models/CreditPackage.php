<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditPackage extends Model
{
    protected $fillable = ['name', 'credit_amount', 'price', 'bonus_points', 'is_active'];
}
