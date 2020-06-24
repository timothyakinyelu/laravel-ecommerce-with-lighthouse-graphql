<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    //Relationships
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot('unit');
    }

    public function orderCost(): HasMany
    {
        return $this->hasMany(OrderCostDetail::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
