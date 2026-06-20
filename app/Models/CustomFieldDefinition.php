<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomFieldDefinition extends Model
{
    protected $fillable = [
        'entity_type', 'slug', 'label_bn', 'label_en', 'field_type', 'options',
        'form_section', 'is_required', 'is_filterable', 'is_visible_to_bari_rep',
        'display_order', 'is_active', 'default_value', 'help_text_bn', 'help_text_en',
        'validation_rules', 'created_by',
    ];

    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
        'is_visible_to_bari_rep' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForEntity($query, string $entityType)
    {
        return $query->where('entity_type', $entityType);
    }
}
