<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;


class BillingAddress extends Model
{
    //Relationships
    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }
}
