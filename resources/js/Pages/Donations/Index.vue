<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ donations: Object, filters: Object, summary: Object });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('donations')" />
    <AppLayout>
        <h1 class="text-2xl font-bold mb-6">{{ t('donations') }}</h1>
        <div class="grid grid-cols-2 gap-4 mb-6 max-w-md">
            <div class="bg-white rounded-xl border p-4"><p class="text-sm text-slate-500">{{ t('given') }}</p><p class="text-xl font-bold text-primary-700">{{ Number(summary?.total_given || 0).toLocaleString() }} BDT</p></div>
            <div class="bg-white rounded-xl border p-4"><p class="text-sm text-slate-500">{{ t('received') }}</p><p class="text-xl font-bold text-accent-600">{{ Number(summary?.total_received || 0).toLocaleString() }} BDT</p></div>
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
                        <td class="px-4 py-3"><span :class="d.type === 'given' ? 'text-primary-700' : 'text-accent-600'">{{ d.type }}</span></td>
                        <td class="px-4 py-3 font-medium">{{ Number(d.amount).toLocaleString() }} BDT</td>
                        <td class="px-4 py-3">{{ d.donor_or_recipient_name || d.resident?.name_bn }}</td>
                        <td class="px-4 py-3"><Link :href="route('donations.show', d.id)" class="text-primary-700">View</Link></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
