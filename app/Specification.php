<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Specification extends Model
{
    //Relationships

    public function products(): BelongsToMany  
    {
        return $this->belongsToMany(Product::class)->withPivot('name');
    }
}
