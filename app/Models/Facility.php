<?php

namespace App\Models;

use App\Traits\HasCustomFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facility extends Model
{
    use HasCustomFields;

    protected $fillable = [
        'union_id', 'village_id', 'facility_type_id',
        'name_bn', 'name_en', 'address', 'contact_phone',
    ];

    public function union(): BelongsTo
    {
        return $this->belongsTo(UnionProfile::class, 'union_id');
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function facilityType(): BelongsTo
    {
        return $this->belongsTo(FacilityType::class);
    }
}
