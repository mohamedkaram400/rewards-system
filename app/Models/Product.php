<?php

namespace App\Models;

use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use Searchable;

    protected $fillable = ['name', 'description', 'price', 'point_cost', 'is_offer_pool', 'category_id'];

    public function searchableAs(): string
    {
         return env('SCOUT_PREFIX') .'_products';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'point_cost' => $this->point_cost,
            'is_offer_pool' => $this->is_offer_pool,
            'category_id' => $this->category_id,
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
