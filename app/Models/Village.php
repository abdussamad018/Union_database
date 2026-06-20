<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Village extends Model
{
    protected $fillable = ['union_id', 'ward_number', 'name_bn', 'name_en'];

    public function union(): BelongsTo
    {
        return $this->belongsTo(UnionProfile::class, 'union_id');
    }

    public function houses(): HasMany
    {
        return $this->hasMany(House::class);
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }
}
