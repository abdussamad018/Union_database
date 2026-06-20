<?php

namespace App\Models;

use App\Traits\HasCustomFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class House extends Model
{
    use HasCustomFields, SoftDeletes;

    protected $fillable = [
        'village_id', 'house_name', 'address', 'representative_user_id',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function representative(): BelongsTo
    {
        return $this->belongsTo(User::class, 'representative_user_id');
    }

    public function residents(): HasMany
    {
        return $this->hasMany(Resident::class);
    }
}
