<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import ComparisonCharts from '@/Components/ComparisonCharts.vue';

defineProps({ stats: Object, house: Object });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('dashboard')" />
    <AppLayout>
        <h1 class="text-2xl font-bold text-slate-900 mb-2">{{ t('dashboard') }}</h1>
        <p v-if="house" class="text-slate-600 mb-6">
            {{ t('house_scope') }}: {{ house?.house_name }} — Ward {{ house?.village?.ward_number }}
        </p>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <StatCard :label="t('total_members')" :value="stats.total_members" />
            <StatCard :label="t('complete_profiles')" :value="stats.complete_profiles" />
            <StatCard :label="t('incomplete_profiles')" :value="stats.incomplete_profiles" color="accent" />
            <StatCard :label="t('donation_receivers')" :value="stats.donation_receivers" />
            <StatCard :label="t('zakat_payers')" :value="stats.zakat_payers" />
            <StatCard :label="t('zakat_does_not_pay')" :value="stats.zakat_non_payers" color="accent" />
            <StatCard label="Govt" :value="stats.govt_running" />
            <StatCard label="Private" :value="stats.private_running" />
        </div>

        <h2 class="text-lg font-semibold text-slate-800 mb-4">{{ t('population_comparisons') }}</h2>
        <ComparisonCharts :charts="stats.charts" class="mb-8" />

        <Link :href="route('bari.residents.index')" class="inline-flex px-6 py-3 bg-primary-700 text-white rounded-lg font-medium hover:bg-primary-800">{{ t('residents') }}</Link>
    </AppLayout>
</template>
