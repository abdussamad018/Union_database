<?php

namespace App\Services;

use App\Enums\CustomFieldEnums;
use App\Models\CustomFieldDefinition;
use App\Models\CustomFieldValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CustomFieldService
{
    public function getDefinitions(string $entityType, ?string $role = null): Collection
    {
        $query = CustomFieldDefinition::active()
            ->forEntity($entityType)
            ->orderBy('display_order');

        if ($role === 'bari_representative') {
            $query->where('is_visible_to_bari_rep', true);
        }

        return $query->get();
    }

    public function getValuesForForm(Model $entity): array
    {
        $values = [];
        foreach ($entity->customFieldValues()->with('definition')->get() as $cfv) {
            $values[$cfv->definition->slug] = $this->extractValue($cfv);
        }

        return $values;
    }

    public function validate(string $entityType, array $data, ?string $role = null): array
    {
        $definitions = $this->getDefinitions($entityType, $role);
        $rules = [];
        $messages = [];

        foreach ($definitions as $def) {
            $fieldRules = [];
            if ($def->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            $fieldRules = array_merge($fieldRules, $this->typeRules($def));

            if ($def->validation_rules) {
                if (isset($def->validation_rules['min'])) $fieldRules[] = 'min:' . $def->validation_rules['min'];
                if (isset($def->validation_rules['max'])) $fieldRules[] = 'max:' . $def->validation_rules['max'];
            }

            $rules["custom_fields.{$def->slug}"] = $fieldRules;
        }

        return Validator::make(['custom_fields' => $data], $rules, $messages)->validate()['custom_fields'] ?? [];
    }

    public function syncValues(Model $entity, array $data, ?string $role = null): void
    {
        $entityType = $entity->getMorphClass();
        $validated = $this->validate($entityType, $data, $role);
        $definitions = $this->getDefinitions($entityType, $role)->keyBy('slug');

        foreach ($validated as $slug => $value) {
            $def = $definitions->get($slug);
            if (!$def) continue;

            CustomFieldValue::updateOrCreate(
                [
                    'custom_field_definition_id' => $def->id,
                    'entity_type' => $entity->getMorphClass(),
                    'entity_id' => $entity->id,
                ],
                $this->prepareValueColumns($def, $value)
            );
        }
    }

    public function checkRequiredComplete(Model $entity, string $entityType, ?string $role = null): bool
    {
        $definitions = $this->getDefinitions($entityType, $role)->where('is_required', true);
        $values = $this->getValuesForForm($entity);

        foreach ($definitions as $def) {
            $val = $values[$def->slug] ?? null;
            if ($val === null || $val === '' || $val === []) {
                return false;
            }
        }

        return true;
    }

    public function generateSlug(string $label, string $entityType): string
    {
        $base = Str::slug($label, '_');
        $slug = $base;
        $i = 1;
        while (CustomFieldDefinition::where('entity_type', $entityType)->where('slug', $slug)->exists()) {
            $slug = $base . '_' . $i++;
        }

        return $slug;
    }

    protected function typeRules(CustomFieldDefinition $def): array
    {
        return match ($def->field_type) {
            'number' => ['integer'],
            'decimal' => ['numeric'],
            'boolean' => ['boolean'],
            'date' => ['date'],
            'email' => ['email'],
            'select' => ['string'],
            'multi_select' => ['array'],
            default => ['string'],
        };
    }

    protected function prepareValueColumns(CustomFieldDefinition $def, mixed $value): array
    {
        if ($def->field_type === 'multi_select' && is_array($value)) {
            $value = json_encode($value);
        }

        return [
            'value' => is_bool($value) ? ($value ? '1' : '0') : (is_array($value) ? json_encode($value) : (string) $value),
            'value_numeric' => in_array($def->field_type, ['number', 'decimal']) ? $value : null,
            'value_date' => $def->field_type === 'date' ? $value : null,
            'value_boolean' => $def->field_type === 'boolean' ? (bool) $value : null,
        ];
    }

    protected function extractValue(CustomFieldValue $cfv): mixed
    {
        $type = $cfv->definition->field_type;

        return match ($type) {
            'boolean' => $cfv->value_boolean,
            'number', 'decimal' => $cfv->value_numeric,
            'date' => $cfv->value_date?->format('Y-m-d'),
            'multi_select' => json_decode($cfv->value ?? '[]', true),
            default => $cfv->value,
        };
    }
}
