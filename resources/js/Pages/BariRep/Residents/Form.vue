<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import ResidentForm from '@/Components/Residents/ResidentForm.vue';

const props = defineProps({
    resident: Object, professionCategories: Array,
    customFieldDefinitions: Array, customFieldValues: Object, formOptions: Object, house: Object,
});
const { t } = useI18n();
const isEdit = !!props.resident;
</script>

<template>
    <Head :title="isEdit ? t('edit') : t('add')" />
    <AppLayout>
        <h1 class="text-2xl font-bold mb-6">{{ isEdit ? t('edit') : t('add') }}</h1>
        <div class="bg-white rounded-xl border p-6">
            <ResidentForm :resident="resident" :profession-categories="professionCategories"
                :custom-field-definitions="customFieldDefinitions" :custom-field-values="customFieldValues"
                :form-options="formOptions" :house="house"
                :submit-route="isEdit ? route('bari.residents.update', resident.id) : route('bari.residents.store')"
                :method="isEdit ? 'put' : 'post'" />
        </div>
    </AppLayout>
</template>
