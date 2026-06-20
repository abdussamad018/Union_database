<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';

defineProps({ stats: Object, house: Object });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('dashboard')" />
    <AppLayout>
        <h1 class="text-2xl font-bold text-slate-900 mb-2">{{ t('dashboard') }}</h1>
        <p v-if="house" class="text-slate-600 mb-6">{{ house?.house_name }} — {{ house?.village?.name_bn }}</p>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <StatCard label="Total Members" :value="stats.total_members" />
            <StatCard label="Complete Profiles" :value="stats.complete_profiles" />
            <StatCard label="Incomplete" :value="stats.incomplete_profiles" color="accent" />
            <StatCard :label="t('donation_receivers')" :value="stats.donation_receivers" />
        </div>
        <Link :href="route('bari.residents.index')" class="inline-flex px-6 py-3 bg-primary-700 text-white rounded-lg font-medium hover:bg-primary-800">{{ t('residents') }}</Link>
    </AppLayout>
</template>
