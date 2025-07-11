<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $category = $this->whenLoaded('category', function ($category) {
            return [
                'id'      => $category->id,
                'name'    => $category->name,
            ];
        });

        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'description'       => $this->description, 
            'price'             => $this->price,
            'point_cost'        => $this->point_cost,
            'is_offer_pool'     => $this->is_offer_pool,
            'category'          => $category,
        ];
    }
}
