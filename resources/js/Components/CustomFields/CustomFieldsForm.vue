<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    definitions: { type: Array, default: () => [] },
    modelValue: { type: Object, default: () => ({}) },
    errors: { type: Object, default: () => ({}) },
    readonly: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue']);
const { locale } = useI18n();

const label = (def) => locale.value === 'bn' ? def.label_bn : def.label_en;
const help = (def) => locale.value === 'bn' ? def.help_text_bn : def.help_text_en;

const update = (slug, val) => {
    emit('update:modelValue', { ...props.modelValue, [slug]: val });
};

const grouped = computed(() => {
    const groups = {};
    for (const def of props.definitions) {
        const section = def.form_section || 'custom';
        if (!groups[section]) groups[section] = [];
        groups[section].push(def);
    }
    return groups;
});
</script>

<template>
    <div v-for="(defs, section) in grouped" :key="section" class="space-y-4">
        <div v-for="def in defs" :key="def.id" class="space-y-1">
            <label class="block text-sm font-medium text-slate-700">
                {{ label(def) }} <span v-if="def.is_required" class="text-red-500">*</span>
            </label>
            <p v-if="help(def)" class="text-xs text-slate-500">{{ help(def) }}</p>

            <input v-if="['text','email','phone'].includes(def.field_type)" :type="def.field_type === 'phone' ? 'tel' : def.field_type"
                :value="modelValue[def.slug] ?? ''" @input="update(def.slug, $event.target.value)" :disabled="readonly"
                class="w-full rounded-lg border-slate-300 focus:border-primary-500 focus:ring-primary-500" />

            <textarea v-else-if="def.field_type === 'textarea'" :value="modelValue[def.slug] ?? ''" @input="update(def.slug, $event.target.value)" :disabled="readonly" rows="3"
                class="w-full rounded-lg border-slate-300 focus:border-primary-500 focus:ring-primary-500" />

            <input v-else-if="['number','decimal'].includes(def.field_type)" type="number" :step="def.field_type === 'decimal' ? '0.01' : '1'"
                :value="modelValue[def.slug] ?? ''" @input="update(def.slug, $event.target.value)" :disabled="readonly"
                class="w-full rounded-lg border-slate-300 focus:border-primary-500 focus:ring-primary-500" />

            <input v-else-if="def.field_type === 'date'" type="date" :value="modelValue[def.slug] ?? ''" @input="update(def.slug, $event.target.value)" :disabled="readonly"
                class="w-full rounded-lg border-slate-300 focus:border-primary-500 focus:ring-primary-500" />

            <label v-else-if="def.field_type === 'boolean'" class="flex items-center gap-2">
                <input type="checkbox" :checked="!!modelValue[def.slug]" @change="update(def.slug, $event.target.checked)" :disabled="readonly"
                    class="rounded border-slate-300 text-primary-600 focus:ring-primary-500" />
                <span class="text-sm text-slate-600">{{ label(def) }}</span>
            </label>

            <select v-else-if="def.field_type === 'select'" :value="modelValue[def.slug] ?? ''" @change="update(def.slug, $event.target.value)" :disabled="readonly"
                class="w-full rounded-lg border-slate-300 focus:border-primary-500 focus:ring-primary-500">
                <option value="">—</option>
                <option v-for="opt in (def.options || [])" :key="opt.value" :value="opt.value">
                    {{ locale === 'bn' ? opt.label_bn : opt.label_en }}
                </option>
            </select>

            <p v-if="errors[`custom_fields.${def.slug}`]" class="text-xs text-red-600">{{ errors[`custom_fields.${def.slug}`] }}</p>
        </div>
    </div>
</template>
