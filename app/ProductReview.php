<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    use SoftDeletes;

    /**
     * The attribute with default values.
     *
     * @var array
     */
    protected $attributes = [
        'is_approved' => 0,

    ];

    //Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
