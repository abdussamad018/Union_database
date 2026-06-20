<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();
const page = usePage();

const switchLocale = (loc) => {
    router.post(route('locale.update'), { locale: loc }, { preserveScroll: true, onSuccess: () => { locale.value = loc; } });
};
</script>

<template>
    <div class="flex items-center gap-1 bg-slate-100 rounded-lg p-1">
        <button @click="switchLocale('bn')" :class="['px-2.5 py-1 text-xs font-medium rounded-md transition', (page.props.locale || locale) === 'bn' ? 'bg-white shadow text-primary-700' : 'text-slate-600']">বাংলা</button>
        <button @click="switchLocale('en')" :class="['px-2.5 py-1 text-xs font-medium rounded-md transition', (page.props.locale || locale) === 'en' ? 'bg-white shadow text-primary-700' : 'text-slate-600']">EN</button>
    </div>
</template>
