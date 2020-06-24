<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    //Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
