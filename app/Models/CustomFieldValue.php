<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CustomFieldValue extends Model
{
    protected $fillable = [
        'custom_field_definition_id', 'entity_type', 'entity_id',
        'value', 'value_numeric', 'value_date', 'value_boolean',
    ];

    protected $casts = [
        'value_date' => 'date',
        'value_boolean' => 'boolean',
        'value_numeric' => 'decimal:4',
    ];

    public function definition(): BelongsTo
    {
        return $this->belongsTo(CustomFieldDefinition::class, 'custom_field_definition_id');
    }

    public function entity(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'entity_type', 'entity_id');
    }
}
