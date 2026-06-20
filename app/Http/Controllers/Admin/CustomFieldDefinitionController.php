<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CustomFieldEnums;
use App\Http\Controllers\Controller;
use App\Models\CustomFieldDefinition;
use App\Services\CustomFieldService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomFieldDefinitionController extends Controller
{
    public function __construct(protected CustomFieldService $customFieldService) {}

    public function index()
    {
        $definitions = CustomFieldDefinition::orderBy('entity_type')->orderBy('display_order')->get()
            ->groupBy('entity_type');

        return Inertia::render('Admin/CustomFields/Index', [
            'definitions' => $definitions,
            'entityTypes' => CustomFieldEnums::ENTITY_TYPES,
            'fieldTypes' => CustomFieldEnums::FIELD_TYPES,
            'formSections' => CustomFieldEnums::FORM_SECTIONS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'entity_type' => 'required|in:resident,house,donation,facility',
            'label_bn' => 'required|string|max:255',
            'label_en' => 'required|string|max:255',
            'field_type' => 'required|string',
            'form_section' => 'required|string',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'is_filterable' => 'boolean',
            'is_visible_to_bari_rep' => 'boolean',
            'help_text_bn' => 'nullable|string',
            'help_text_en' => 'nullable|string',
        ]);

        $data['slug'] = $this->customFieldService->generateSlug($data['label_en'], $data['entity_type']);
        $data['created_by'] = auth()->id();
        $data['display_order'] = CustomFieldDefinition::where('entity_type', $data['entity_type'])->max('display_order') + 1;

        CustomFieldDefinition::create($data);

        return back()->with('success', 'Custom field created.');
    }

    public function update(Request $request, CustomFieldDefinition $custom_field_definition)
    {
        $data = $request->validate([
            'label_bn' => 'required|string|max:255',
            'label_en' => 'required|string|max:255',
            'field_type' => 'required|string',
            'form_section' => 'required|string',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'is_filterable' => 'boolean',
            'is_visible_to_bari_rep' => 'boolean',
            'help_text_bn' => 'nullable|string',
            'help_text_en' => 'nullable|string',
        ]);

        $custom_field_definition->update($data);
        return back()->with('success', 'Custom field updated.');
    }

    public function toggle(CustomFieldDefinition $custom_field_definition)
    {
        $custom_field_definition->update(['is_active' => !$custom_field_definition->is_active]);
        return back()->with('success', 'Field status updated.');
    }

    public function reorder(Request $request)
    {
        foreach ($request->input('order', []) as $item) {
            CustomFieldDefinition::where('id', $item['id'])->update(['display_order' => $item['order']]);
        }
        return back()->with('success', 'Order updated.');
    }
}
