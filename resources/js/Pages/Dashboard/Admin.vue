<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import ComparisonCharts from '@/Components/ComparisonCharts.vue';

defineProps({ stats: Object });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('dashboard')" />
    <AppLayout>
        <h1 class="text-2xl font-bold text-slate-900 mb-6">{{ t('dashboard') }}</h1>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <StatCard :label="t('total_population')" :value="stats.total_residents" />
            <StatCard :label="t('total_houses')" :value="stats.total_houses" />
            <StatCard :label="t('donation_givers')" :value="stats.donation_givers" />
            <StatCard :label="t('donation_receivers')" :value="stats.donation_receivers" color="accent" />
            <StatCard :label="t('urgent_aid')" :value="stats.urgent_aid" color="accent" />
            <StatCard :label="t('zakat_payers')" :value="stats.zakat_payers" />
            <StatCard label="Govt (Running)" :value="stats.govt_running" />
            <StatCard label="Private (Running)" :value="stats.private_running" />
        </div>

        <h2 class="text-lg font-semibold text-slate-800 mb-4">{{ t('population_comparisons') }}</h2>
        <ComparisonCharts :charts="stats.charts" class="mb-8" />

        <div class="grid lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border p-6">
                <h3 class="font-semibold text-slate-800 mb-4">{{ t('donation_summary') }}</h3>
                <div class="flex gap-8">
                    <div><p class="text-sm text-slate-500">{{ t('given') }}</p><p class="text-xl font-bold text-primary-700">{{ Number(stats.donations_given).toLocaleString() }}</p></div>
                    <div><p class="text-sm text-slate-500">{{ t('received') }}</p><p class="text-xl font-bold text-accent-600">{{ Number(stats.donations_received).toLocaleString() }}</p></div>
                </div>
            </div>
            <div class="bg-white rounded-xl border p-6">
                <h3 class="font-semibold text-slate-800 mb-4">{{ t('ward_population') }}</h3>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    <div v-for="v in stats.village_population" :key="v.id" class="flex justify-between text-sm">
                        <span>{{ v.name_bn }} (Ward {{ v.ward_number }})</span>
                        <span class="font-medium">{{ v.residents_count }} / {{ v.houses_count }}</span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
