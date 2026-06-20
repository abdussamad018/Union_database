<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({ users: Object, roles: Array, houses: Array });
const { t } = useI18n();

const bariRole = 'bari_representative';
const showEditModal = ref(false);

const form = useForm({
    name: '',
    name_bn: '',
    email: '',
    phone: '',
    password: '',
    role: bariRole,
    house_id: '',
    locale: 'bn',
});

const editForm = useForm({
    id: null,
    name: '',
    name_bn: '',
    phone: '',
    password: '',
    role: bariRole,
    house_id: '',
    locale: 'bn',
    is_active: true,
});

const isBariRole = (role) => role === bariRole;

const availableHouses = (currentHouseId = null) => props.houses.filter((house) => {
    if (currentHouseId && String(house.id) === String(currentHouseId)) return true;
    return !house.representative_user_id;
});

const createHouses = computed(() => availableHouses());
const editHouses = computed(() => availableHouses(editForm.house_id));

const houseLabel = (house) => {
    const ward = house.village?.ward_number ? `Ward ${house.village.ward_number}` : '';
    return [ward, house.house_name].filter(Boolean).join(' — ');
};

const roleLabel = (role) => props.roles.find((r) => r.value === role)?.label || role;

const submit = () => {
    form.post(route('admin.users.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('name', 'name_bn', 'email', 'phone', 'password', 'house_id'),
    });
};

const openEdit = (user) => {
    editForm.clearErrors();
    editForm.id = user.id;
    editForm.name = user.name;
    editForm.name_bn = user.name_bn || '';
    editForm.phone = user.phone || '';
    editForm.password = '';
    editForm.role = user.role;
    editForm.house_id = user.house_id || '';
    editForm.locale = user.locale || 'bn';
    editForm.is_active = user.is_active;
    showEditModal.value = true;
};

const closeEdit = () => {
    showEditModal.value = false;
    editForm.reset();
};

const submitEdit = () => {
    editForm.put(route('admin.users.update', editForm.id), {
        preserveScroll: true,
        onSuccess: () => closeEdit(),
    });
};

const toggleActive = (user) => {
    router.post(route('admin.users.toggle-active', user.id), {}, { preserveScroll: true });
};

const onRoleChange = (target) => {
    if (!isBariRole(target.role)) {
        target.house_id = '';
    }
};
</script>

<template>
    <Head :title="t('users')" />
    <AppLayout>
        <h1 class="text-2xl font-bold text-slate-900 mb-6">{{ t('users') }}</h1>

        <form @submit.prevent="submit" class="bg-white rounded-xl border p-4 mb-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-3">{{ t('add_user') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <input v-model="form.name" :placeholder="t('name_en')" class="rounded-lg border-slate-300 text-sm" required />
                <input v-model="form.name_bn" :placeholder="t('name_bn')" class="rounded-lg border-slate-300 text-sm" />
                <input v-model="form.email" type="email" placeholder="Email" class="rounded-lg border-slate-300 text-sm" required />
                <input v-model="form.phone" type="tel" :placeholder="t('phone')" class="rounded-lg border-slate-300 text-sm" :required="isBariRole(form.role)" />
                <input v-model="form.password" type="password" :placeholder="t('password')" class="rounded-lg border-slate-300 text-sm" required />
                <select v-model="form.role" @change="onRoleChange(form)" class="rounded-lg border-slate-300 text-sm">
                    <option v-for="r in roles" :key="r.value" :value="r.value">{{ r.label }}</option>
                </select>
                <select v-if="isBariRole(form.role)" v-model="form.house_id" class="rounded-lg border-slate-300 text-sm" required>
                    <option value="">{{ t('select_house') }}</option>
                    <option v-for="house in createHouses" :key="house.id" :value="house.id">{{ houseLabel(house) }}</option>
                </select>
            </div>
            <InputError class="mt-2" :message="form.errors.house_id" />
            <InputError class="mt-1" :message="form.errors.phone" />
            <InputError class="mt-1" :message="form.errors.email" />
            <button type="submit" :disabled="form.processing" class="mt-3 px-4 py-2 bg-primary-700 text-white rounded-lg text-sm font-medium disabled:opacity-50">{{ t('add') }}</button>
        </form>

        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left">{{ t('name_bn') }}</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">{{ t('phone') }}</th>
                        <th class="px-4 py-3 text-left">{{ t('role') }}</th>
                        <th class="px-4 py-3 text-left">{{ t('house') }}</th>
                        <th class="px-4 py-3 text-left">{{ t('status') }}</th>
                        <th class="px-4 py-3 text-right">{{ t('actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="u in users.data" :key="u.id" class="border-t hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ u.name_bn || u.name }}</p>
                            <p v-if="u.name_bn" class="text-xs text-slate-500">{{ u.name }}</p>
                        </td>
                        <td class="px-4 py-3">{{ u.email }}</td>
                        <td class="px-4 py-3">{{ u.phone || '—' }}</td>
                        <td class="px-4 py-3">{{ roleLabel(u.role) }}</td>
                        <td class="px-4 py-3">
                            <template v-if="u.house">
                                Ward {{ u.house.village?.ward_number }} — {{ u.house.house_name }}
                            </template>
                            <span v-else class="text-slate-400">—</span>
                        </td>
                        <td class="px-4 py-3">
                            <span :class="['px-2 py-0.5 rounded text-xs', u.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                {{ u.is_active ? t('active') : t('inactive') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                            <button @click="openEdit(u)" class="text-primary-700 hover:underline">{{ t('edit') }}</button>
                            <button @click="toggleActive(u)" :class="u.is_active ? 'text-amber-700 hover:underline' : 'text-green-700 hover:underline'">
                                {{ u.is_active ? t('deactivate') : t('activate') }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="!users.data?.length" class="p-8 text-center text-slate-500">{{ t('no_results') }}</div>
        </div>

        <Modal :show="showEditModal" @close="closeEdit">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">{{ t('edit_user') }}</h2>
                <form @submit.prevent="submitEdit" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input v-model="editForm.name" :placeholder="t('name_en')" class="rounded-lg border-slate-300 text-sm w-full" required />
                        <input v-model="editForm.name_bn" :placeholder="t('name_bn')" class="rounded-lg border-slate-300 text-sm w-full" />
                        <input v-model="editForm.phone" type="tel" :placeholder="t('phone')" class="rounded-lg border-slate-300 text-sm w-full" :required="isBariRole(editForm.role)" />
                        <input v-model="editForm.password" type="password" :placeholder="t('new_password_optional')" class="rounded-lg border-slate-300 text-sm w-full" />
                        <select v-model="editForm.role" @change="onRoleChange(editForm)" class="rounded-lg border-slate-300 text-sm w-full">
                            <option v-for="r in roles" :key="r.value" :value="r.value">{{ r.label }}</option>
                        </select>
                        <select v-if="isBariRole(editForm.role)" v-model="editForm.house_id" class="rounded-lg border-slate-300 text-sm w-full" required>
                            <option value="">{{ t('select_house') }}</option>
                            <option v-for="house in editHouses" :key="house.id" :value="house.id">{{ houseLabel(house) }}</option>
                        </select>
                    </div>
                    <InputError :message="editForm.errors.house_id" />
                    <InputError :message="editForm.errors.phone" />
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="closeEdit" class="px-4 py-2 border rounded-lg text-sm">{{ t('cancel') }}</button>
                        <button type="submit" :disabled="editForm.processing" class="px-4 py-2 bg-primary-700 text-white rounded-lg text-sm font-medium disabled:opacity-50">{{ t('save') }}</button>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>
