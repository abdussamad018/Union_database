<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({ villages: Object, union: Object });
const { t } = useI18n();
const showEditModal = ref(false);

const form = useForm({
    ward_number: '',
    name_bn: '',
    name_en: '',
});

const editForm = useForm({
    id: null,
    ward_number: '',
    name_bn: '',
    name_en: '',
});

const submit = () => {
    form.post(route('admin.villages.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const openEdit = (village) => {
    editForm.clearErrors();
    editForm.id = village.id;
    editForm.ward_number = village.ward_number;
    editForm.name_bn = village.name_bn;
    editForm.name_en = village.name_en;
    showEditModal.value = true;
};

const closeEdit = () => {
    showEditModal.value = false;
    editForm.reset();
};

const submitEdit = () => {
    editForm.put(route('admin.villages.update', editForm.id), {
        preserveScroll: true,
        onSuccess: () => closeEdit(),
    });
};
</script>

<template>
    <Head :title="t('villages')" />
    <AppLayout>
        <h1 class="text-2xl font-bold text-slate-900 mb-2">{{ t('villages') }}</h1>
        <p v-if="union" class="text-sm text-slate-500 mb-6">{{ union.name_bn }}</p>

        <form @submit.prevent="submit" class="bg-white rounded-xl border p-4 mb-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-3">{{ t('add_village') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input v-model="form.ward_number" type="number" min="1" :placeholder="t('ward_number')" class="rounded-lg border-slate-300 text-sm" required />
                <input v-model="form.name_bn" :placeholder="t('name_bn')" class="rounded-lg border-slate-300 text-sm" required />
                <input v-model="form.name_en" :placeholder="t('name_en')" class="rounded-lg border-slate-300 text-sm" required />
                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-primary-700 text-white rounded-lg text-sm font-medium disabled:opacity-50">{{ t('add') }}</button>
            </div>
            <InputError class="mt-2" :message="form.errors.ward_number" />
            <InputError class="mt-1" :message="form.errors.name_bn" />
        </form>

        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left">{{ t('ward_number') }}</th>
                        <th class="px-4 py-3 text-left">{{ t('name_bn') }}</th>
                        <th class="px-4 py-3 text-left">{{ t('name_en') }}</th>
                        <th class="px-4 py-3 text-left">{{ t('houses') }}</th>
                        <th class="px-4 py-3 text-right">{{ t('actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="v in villages.data" :key="v.id" class="border-t hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">Ward {{ v.ward_number }}</td>
                        <td class="px-4 py-3">{{ v.name_bn }}</td>
                        <td class="px-4 py-3">{{ v.name_en }}</td>
                        <td class="px-4 py-3">{{ v.houses_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <button @click="openEdit(v)" class="text-primary-700 hover:underline">{{ t('edit') }}</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="!villages.data?.length" class="p-8 text-center text-slate-500">{{ t('no_results') }}</div>
        </div>

        <Modal :show="showEditModal" @close="closeEdit">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">{{ t('edit_village') }}</h2>
                <form @submit.prevent="submitEdit" class="space-y-4">
                    <input v-model="editForm.ward_number" type="number" min="1" :placeholder="t('ward_number')" class="rounded-lg border-slate-300 text-sm w-full" required />
                    <input v-model="editForm.name_bn" :placeholder="t('name_bn')" class="rounded-lg border-slate-300 text-sm w-full" required />
                    <input v-model="editForm.name_en" :placeholder="t('name_en')" class="rounded-lg border-slate-300 text-sm w-full" required />
                    <InputError :message="editForm.errors.ward_number" />
                    <InputError :message="editForm.errors.name_bn" />
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="closeEdit" class="px-4 py-2 border rounded-lg text-sm">{{ t('cancel') }}</button>
                        <button type="submit" :disabled="editForm.processing" class="px-4 py-2 bg-primary-700 text-white rounded-lg text-sm font-medium disabled:opacity-50">{{ t('save') }}</button>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>
