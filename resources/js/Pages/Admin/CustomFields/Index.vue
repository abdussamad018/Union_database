<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ definitions: Object, entityTypes: Array, fieldTypes: Array, formSections: Array });
const { t, locale } = useI18n();

const form = useForm({
    entity_type: 'resident', label_bn: '', label_en: '', field_type: 'text',
    form_section: 'custom', options: [], is_required: false, is_filterable: false, is_visible_to_bari_rep: true,
});

const submit = () => form.post(route('admin.custom-fields.store'), { onSuccess: () => form.reset() });
const toggle = (id) => router.post(route('admin.custom-fields.toggle', id));
const label = (def) => locale.value === 'bn' ? def.label_bn : def.label_en;
</script>

<template>
    <Head :title="t('custom_fields')" />
    <AppLayout>
        <h1 class="text-2xl font-bold mb-6">{{ t('custom_fields') }}</h1>

        <div class="grid lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border p-6">
                <h2 class="font-semibold mb-4">Add New Field</h2>
                <form @submit.prevent="submit" class="space-y-3">
                    <select v-model="form.entity_type" class="w-full rounded-lg border-slate-300"><option v-for="e in entityTypes" :key="e" :value="e">{{ e }}</option></select>
                    <input v-model="form.label_bn" placeholder="Label (BN)" class="w-full rounded-lg border-slate-300" required />
                    <input v-model="form.label_en" placeholder="Label (EN)" class="w-full rounded-lg border-slate-300" required />
                    <select v-model="form.field_type" class="w-full rounded-lg border-slate-300"><option v-for="f in fieldTypes" :key="f" :value="f">{{ f }}</option></select>
                    <select v-model="form.form_section" class="w-full rounded-lg border-slate-300"><option v-for="s in formSections" :key="s" :value="s">{{ s }}</option></select>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" v-model="form.is_required" class="rounded" /> Required</label>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" v-model="form.is_filterable" class="rounded" /> Filterable</label>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" v-model="form.is_visible_to_bari_rep" class="rounded" /> Visible to Bari Rep</label>
                    <button type="submit" class="px-4 py-2 bg-primary-700 text-white rounded-lg text-sm" :disabled="form.processing">{{ t('add') }}</button>
                </form>
            </div>

            <div class="space-y-4">
                <div v-for="(defs, entity) in definitions" :key="entity" class="bg-white rounded-xl border p-6">
                    <h3 class="font-semibold mb-3 capitalize">{{ entity }}</h3>
                    <div v-for="def in defs" :key="def.id" class="flex items-center justify-between py-2 border-t text-sm">
                        <div>
                            <span class="font-medium">{{ label(def) }}</span>
                            <span class="text-slate-400 ml-2">({{ def.field_type }})</span>
                            <span v-if="!def.is_active" class="ml-2 text-red-500 text-xs">inactive</span>
                        </div>
                        <button @click="toggle(def.id)" class="text-primary-700 hover:underline">{{ def.is_active ? 'Deactivate' : 'Activate' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
