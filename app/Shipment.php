<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    //Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
