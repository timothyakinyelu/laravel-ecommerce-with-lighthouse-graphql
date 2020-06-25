<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    /**
     * The attribute with default values.
     *
     * @var array
     */
    protected $attributes = [
        'is_featured' => 0,
        'allow_reviews' => 0

    ];

    public function setSlugAttribute($value) {
        // grab the name and slugify it
        $this->attributes['slug'] = Str::slug($this->name);
    }

    //Relationships
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function productImage(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function productInventory(): HasMany
    {
        return $this->hasMany(ProductInventory::class);
    }

    public function productPrice(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function productReview(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function productShipping(): HasMany
    {
        return $this->hasMany(ProductShipping::class);
    }
}
