<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasPermissionTrait;

class Role extends Model
{
    use SoftDeletes, HasPermissionTrait;

    /**
     * The attribute with default values.
     *
     * @var array
     */
    protected $attributes = [
        'is_active' => 0,
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    //Relationships
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }
}
