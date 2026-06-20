<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnionProfile extends Model
{
    protected $table = 'unions';

    protected $fillable = [
        'name_bn', 'name_en', 'description_bn', 'description_en',
        'contact_phone', 'contact_email',
    ];

    public function villages(): HasMany
    {
        return $this->hasMany(Village::class, 'union_id');
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class, 'union_id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'union_id');
    }
}
