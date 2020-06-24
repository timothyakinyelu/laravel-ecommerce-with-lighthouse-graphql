<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    //Relationship
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
