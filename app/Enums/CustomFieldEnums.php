<?php

namespace App\Enums;

class CustomFieldEnums
{
    public const ENTITY_TYPES = ['resident', 'house', 'donation', 'facility'];
    public const FIELD_TYPES = ['text', 'textarea', 'number', 'decimal', 'boolean', 'date', 'select', 'multi_select', 'email', 'phone'];
    public const FORM_SECTIONS = ['personal', 'household', 'employment', 'charity', 'vulnerability', 'custom'];
}
