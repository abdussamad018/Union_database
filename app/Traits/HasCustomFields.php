<?php

namespace App\Traits;

use App\Models\CustomFieldValue;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasCustomFields
{
    public function customFieldValues(): MorphMany
    {
        return $this->morphMany(CustomFieldValue::class, 'entity', 'entity_type', 'entity_id');
    }

    public function getCustomFieldValue(string $slug): mixed
    {
        $value = $this->customFieldValues()
            ->whereHas('definition', fn ($q) => $q->where('slug', $slug))
            ->with('definition')
            ->first();

        return $value?->value ?? $value?->value_boolean ?? $value?->value_numeric ?? $value?->value_date;
    }
}
