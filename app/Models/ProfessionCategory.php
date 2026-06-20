<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProfessionCategory extends Model
{
    protected $fillable = ['slug', 'name_bn', 'name_en'];

    public function residents(): BelongsToMany
    {
        return $this->belongsToMany(Resident::class, 'resident_profession_category');
    }
}
