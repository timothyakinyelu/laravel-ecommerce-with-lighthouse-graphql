<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use SoftDeletes;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attribute with default values.
     *
     * @var array
     */
    protected $attributes = [
        'is_published' => 0,
    ];

    public function setSlugAttribute($value) {
        // grab the name and slugify it
        $this->attributes['slug'] = Str::slug($this->name);
    }

    //Relationships
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
