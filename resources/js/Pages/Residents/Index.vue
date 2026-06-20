<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ residents: Object, filters: Object, isAdmin: { type: Boolean, default: false } });
const { t } = useI18n();
const base = props.isAdmin ? 'admin.residents' : 'bari.residents';

const destroy = (id) => { if (confirm('Delete?')) router.delete(route(`${base}.destroy`, id)); };
const verify = (id) => router.post(route('bari.residents.verify', id));
</script>

<template>
    <Head :title="t('residents')" />
    <AppLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-slate-900">{{ t('residents') }}</h1>
            <div class="flex gap-2">
                <Link v-if="isAdmin" :href="route('admin.residents.export')" class="px-4 py-2 border rounded-lg text-sm hover:bg-slate-50">{{ t('export_csv') }}</Link>
                <Link :href="route(`${base}.create`)" class="px-4 py-2 bg-primary-700 text-white rounded-lg text-sm font-medium">{{ t('add') }}</Link>
            </div>
        </div>
        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left">
                    <tr>
                        <th class="px-4 py-3">{{ t('name_bn') }}</th>
                        <th class="px-4 py-3">Gender</th>
                        <th class="px-4 py-3" v-if="isAdmin">Village</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Priority</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="r in residents.data" :key="r.id" class="border-t hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ r.name_bn }}</td>
                        <td class="px-4 py-3">{{ r.gender }}</td>
                        <td class="px-4 py-3" v-if="isAdmin">{{ r.house?.village?.name_bn }}</td>
                        <td class="px-4 py-3"><span :class="['px-2 py-0.5 rounded text-xs', r.profile_status === 'complete' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800']">{{ r.profile_status }}</span></td>
                        <td class="px-4 py-3">{{ r.aid_priority }}</td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <Link :href="route(`${base}.edit`, r.id)" class="text-primary-700 hover:underline">{{ t('edit') }}</Link>
                            <button v-if="!isAdmin && r.profile_status !== 'complete'" @click="verify(r.id)" class="text-green-700 hover:underline">{{ t('verify_complete') }}</button>
                            <button @click="destroy(r.id)" class="text-red-600 hover:underline">{{ t('delete') }}</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="!residents.data?.length" class="p-8 text-center text-slate-500">No residents found.</div>
        </div>
    </AppLayout>
</template>
