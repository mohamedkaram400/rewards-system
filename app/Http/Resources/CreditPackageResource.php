<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
        'id'                => $this->id,
        'name'              => $this->name,
        'credit_amount'     => $this->credit_amount,
        'price'             => $this->price,
        'bonus_points'      => $this->bonus_points,
        'is_active'         => $this->is_active,
        ];
    }
}
