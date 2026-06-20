<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import StatCard from '@/Components/StatCard.vue';

defineProps({ union: Object, stats: Object });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('app_name')" />
    <div class="min-h-screen bg-gradient-to-b from-primary-50 to-white">
        <header class="max-w-7xl mx-auto px-4 sm:px-6 py-6 flex items-center justify-between">
            <h1 class="text-xl font-bold text-primary-800">{{ union?.name_bn || t('app_name') }}</h1>
            <div class="flex items-center gap-4">
                <LanguageSwitcher />
                <Link :href="route('login')" class="px-5 py-2.5 bg-primary-700 text-white rounded-lg font-medium hover:bg-primary-800">{{ t('login') }}</Link>
            </div>
        </header>

        <section class="max-w-7xl mx-auto px-4 sm:px-6 py-16 text-center">
            <h2 class="text-4xl sm:text-5xl font-bold text-slate-900 leading-tight">{{ t('landing_hero') }}</h2>
            <p class="mt-4 text-lg text-slate-600 max-w-2xl mx-auto">{{ union?.description_bn || t('landing_sub') }}</p>
            <Link :href="route('login')" class="inline-block mt-8 px-8 py-3 bg-accent-500 text-white rounded-xl font-semibold hover:bg-accent-600 shadow-lg">{{ t('login') }}</Link>
        </section>

        <section class="max-w-7xl mx-auto px-4 sm:px-6 pb-20">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <StatCard :label="t('total_population')" :value="stats?.total_residents" />
                <StatCard :label="t('total_houses')" :value="stats?.total_houses" />
                <StatCard :label="t('total_villages')" :value="stats?.total_villages" />
                <StatCard v-if="stats?.facilities?.high_school" label="High Schools" :value="stats.facilities.high_school" color="accent" />
                <StatCard v-if="stats?.facilities?.primary_school" label="Primary Schools" :value="stats.facilities.primary_school" />
                <StatCard v-if="stats?.facilities?.madrasa" label="Madrasa" :value="stats.facilities.madrasa" />
                <StatCard v-if="stats?.facilities?.mosque" label="Mosque" :value="stats.facilities.mosque" />
                <StatCard v-if="stats?.facilities?.social_organization" label="Social Org" :value="stats.facilities.social_organization" color="accent" />
            </div>
        </section>

        <footer class="border-t bg-white py-8 text-center text-sm text-slate-500">
            {{ union?.name_en }} &copy; {{ new Date().getFullYear() }}
        </footer>
    </div>
</template>
