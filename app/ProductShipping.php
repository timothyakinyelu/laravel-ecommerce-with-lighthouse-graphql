<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductShipping extends Model
{
    /**
     * The attribute with default values.
     *
     * @var array
     */
    protected $attributes = [
        'is_shipping_enabled' => 0,
        'is_delivery_free' => 0

    ];

    //Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
