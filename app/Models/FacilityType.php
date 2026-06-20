<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacilityType extends Model
{
    protected $fillable = ['slug', 'name_bn', 'name_en'];

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }
}
