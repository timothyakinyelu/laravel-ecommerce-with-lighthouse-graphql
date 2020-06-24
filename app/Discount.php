<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discount extends Model
{
    use SoftDeletes;

    //Relationships
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }
    
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
