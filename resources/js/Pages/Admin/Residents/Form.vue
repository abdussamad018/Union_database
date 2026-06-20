<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import ResidentForm from '@/Components/Residents/ResidentForm.vue';

const props = defineProps({
    resident: Object, houses: Array, professionCategories: Array,
    customFieldDefinitions: Array, customFieldValues: Object, formOptions: Object,
});

const { t } = useI18n();
const isEdit = !!props.resident;
</script>

<template>
    <Head :title="isEdit ? t('edit') : t('add')" />
    <AppLayout>
        <h1 class="text-2xl font-bold mb-6">{{ isEdit ? t('edit') : t('add') }} — {{ t('residents') }}</h1>
        <div class="bg-white rounded-xl border p-6">
            <ResidentForm :resident="resident" :houses="houses" :profession-categories="professionCategories"
                :custom-field-definitions="customFieldDefinitions" :custom-field-values="customFieldValues"
                :form-options="formOptions" :is-admin="true"
                :submit-route="isEdit ? route('admin.residents.update', resident.id) : route('admin.residents.store')"
                :method="isEdit ? 'put' : 'post'" />
        </div>
    </AppLayout>
</template>
