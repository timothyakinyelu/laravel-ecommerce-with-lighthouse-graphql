<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductInventory extends Model
{
    /**
     * The attribute with default values.
     *
     * @var array
     */
    protected $attributes = [
        'is_returnable' => 0

    ];

    //Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
