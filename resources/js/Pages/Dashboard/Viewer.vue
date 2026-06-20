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
            <StatCard :label="t('donation_givers')" :value="stats.donation_givers" />
            <StatCard :label="t('donation_receivers')" :value="stats.donation_receivers" color="accent" />
            <StatCard :label="t('urgent_aid')" :value="stats.urgent_aid" color="accent" />
            <StatCard :label="t('zakat_payers')" :value="stats.zakat_payers" />
            <StatCard label="Govt (Running)" :value="stats.govt_running" />
            <StatCard label="Private (Running)" :value="stats.private_running" />
            <StatCard :label="t('complete_profiles')" :value="stats.complete_profiles" />
        </div>

        <h2 class="text-lg font-semibold text-slate-800 mb-4">{{ t('population_comparisons') }}</h2>
        <ComparisonCharts :charts="stats.charts" />
    </AppLayout>
</template>
