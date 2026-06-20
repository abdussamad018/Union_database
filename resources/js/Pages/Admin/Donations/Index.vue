<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ donations: Object, filters: Object });
const { t } = useI18n();
const destroy = (id) => { if (confirm('Delete?')) router.delete(route('admin.donations.destroy', id)); };
</script>

<template>
    <Head :title="t('donations')" />
    <AppLayout>
        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold">{{ t('donations') }}</h1>
            <Link :href="route('admin.donations.create')" class="px-4 py-2 bg-primary-700 text-white rounded-lg text-sm">{{ t('add') }}</Link>
        </div>
        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50"><tr>
                    <th class="px-4 py-3 text-left">Date</th><th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">Amount</th><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3"></th>
                </tr></thead>
                <tbody>
                    <tr v-for="d in donations.data" :key="d.id" class="border-t">
                        <td class="px-4 py-3">{{ d.date?.substring?.(0,10) }}</td>
                        <td class="px-4 py-3">{{ d.type }}</td>
                        <td class="px-4 py-3">{{ Number(d.amount).toLocaleString() }}</td>
                        <td class="px-4 py-3">{{ d.donor_or_recipient_name }}</td>
                        <td class="px-4 py-3"><button @click="destroy(d.id)" class="text-red-600">{{ t('delete') }}</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
