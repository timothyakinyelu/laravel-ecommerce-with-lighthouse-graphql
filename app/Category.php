<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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

    protected $fillable = [
        'name', 'parent_id', 'slug', 'description',
        'is_published', 'category_image'
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

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function parent(): BelongsTo 
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
