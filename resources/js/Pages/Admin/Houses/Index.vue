<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({ houses: Object, villages: Array, bariRepresentatives: Array });
const { t } = useI18n();
const showEditModal = ref(false);

const form = useForm({
    village_id: '',
    house_name: '',
    address: '',
    representative_user_id: '',
});

const editForm = useForm({
    id: null,
    village_id: '',
    house_name: '',
    address: '',
    representative_user_id: '',
});

const repLabel = (user) => user.name_bn || user.name;

const availableReps = (currentRepId = null, currentHouseId = null) => props.bariRepresentatives.filter((user) => {
    if (currentRepId && String(user.id) === String(currentRepId)) return true;
    return !user.house_id || String(user.house_id) === String(currentHouseId);
});

const submit = () => {
    form.post(route('admin.houses.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('house_name', 'address', 'representative_user_id'),
    });
};

const openEdit = (house) => {
    editForm.clearErrors();
    editForm.id = house.id;
    editForm.village_id = house.village_id;
    editForm.house_name = house.house_name;
    editForm.address = house.address || '';
    editForm.representative_user_id = house.representative_user_id || '';
    showEditModal.value = true;
};

const closeEdit = () => {
    showEditModal.value = false;
    editForm.reset();
};

const submitEdit = () => {
    editForm.put(route('admin.houses.update', editForm.id), {
        preserveScroll: true,
        onSuccess: () => closeEdit(),
    });
};
</script>

<template>
    <Head :title="t('houses')" />
    <AppLayout>
        <h1 class="text-2xl font-bold text-slate-900 mb-6">{{ t('houses') }}</h1>

        <form @submit.prevent="submit" class="bg-white rounded-xl border p-4 mb-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-3">{{ t('add_house') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                <select v-model="form.village_id" class="rounded-lg border-slate-300 text-sm" required>
                    <option value="">{{ t('select_ward_first') }}</option>
                    <option v-for="v in villages" :key="v.id" :value="v.id">Ward {{ v.ward_number }} — {{ v.name_bn }}</option>
                </select>
                <input v-model="form.house_name" :placeholder="t('house_name')" class="rounded-lg border-slate-300 text-sm" required />
                <input v-model="form.address" :placeholder="t('address')" class="rounded-lg border-slate-300 text-sm" />
                <select v-model="form.representative_user_id" class="rounded-lg border-slate-300 text-sm">
                    <option value="">{{ t('no_representative') }}</option>
                    <option v-for="r in availableReps()" :key="r.id" :value="r.id">{{ repLabel(r) }}</option>
                </select>
                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-primary-700 text-white rounded-lg text-sm font-medium disabled:opacity-50">{{ t('add') }}</button>
            </div>
            <InputError class="mt-2" :message="form.errors.village_id" />
            <InputError class="mt-1" :message="form.errors.house_name" />
        </form>

        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left">{{ t('house_name') }}</th>
                        <th class="px-4 py-3 text-left">{{ t('village') }}</th>
                        <th class="px-4 py-3 text-left">{{ t('address') }}</th>
                        <th class="px-4 py-3 text-left">{{ t('representative') }}</th>
                        <th class="px-4 py-3 text-left">{{ t('residents') }}</th>
                        <th class="px-4 py-3 text-right">{{ t('actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="h in houses.data" :key="h.id" class="border-t hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ h.house_name }}</td>
                        <td class="px-4 py-3">Ward {{ h.village?.ward_number }} — {{ h.village?.name_bn }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ h.address || '—' }}</td>
                        <td class="px-4 py-3">{{ h.representative?.name_bn || h.representative?.name || '—' }}</td>
                        <td class="px-4 py-3">{{ h.residents_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <button @click="openEdit(h)" class="text-primary-700 hover:underline">{{ t('edit') }}</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="!houses.data?.length" class="p-8 text-center text-slate-500">{{ t('no_results') }}</div>
        </div>

        <Modal :show="showEditModal" @close="closeEdit">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">{{ t('edit_house') }}</h2>
                <form @submit.prevent="submitEdit" class="space-y-4">
                    <select v-model="editForm.village_id" class="rounded-lg border-slate-300 text-sm w-full" required>
                        <option v-for="v in villages" :key="v.id" :value="v.id">Ward {{ v.ward_number }} — {{ v.name_bn }}</option>
                    </select>
                    <input v-model="editForm.house_name" :placeholder="t('house_name')" class="rounded-lg border-slate-300 text-sm w-full" required />
                    <input v-model="editForm.address" :placeholder="t('address')" class="rounded-lg border-slate-300 text-sm w-full" />
                    <select v-model="editForm.representative_user_id" class="rounded-lg border-slate-300 text-sm w-full">
                        <option value="">{{ t('no_representative') }}</option>
                        <option v-for="r in availableReps(editForm.representative_user_id, editForm.id)" :key="r.id" :value="r.id">{{ repLabel(r) }}</option>
                    </select>
                    <InputError :message="editForm.errors.village_id" />
                    <InputError :message="editForm.errors.house_name" />
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="closeEdit" class="px-4 py-2 border rounded-lg text-sm">{{ t('cancel') }}</button>
                        <button type="submit" :disabled="editForm.processing" class="px-4 py-2 bg-primary-700 text-white rounded-lg text-sm font-medium disabled:opacity-50">{{ t('save') }}</button>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>
