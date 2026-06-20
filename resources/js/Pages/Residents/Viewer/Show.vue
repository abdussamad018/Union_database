<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ resident: Object });
const { t, locale } = useI18n();
const profLabel = (p) => locale.value === 'bn' ? p.name_bn : p.name_en;
</script>

<template>
    <Head :title="resident.name_bn" />
    <AppLayout>
        <Link :href="route('residents.viewer.index')" class="text-primary-700 text-sm mb-4 inline-block">&larr; {{ t('residents') }}</Link>

        <div class="bg-white rounded-xl border p-6 max-w-3xl">
            <h1 class="text-2xl font-bold text-slate-900 mb-1">{{ resident.name_bn }}</h1>
            <p v-if="resident.name_en" class="text-slate-500 mb-4">{{ resident.name_en }}</p>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-slate-500">{{ t('village') }}:</span> {{ resident.village?.name_bn }} — {{ resident.house_name }}</div>
                <div><span class="text-slate-500">{{ t('father_name') }}:</span> {{ resident.father_name || '—' }}</div>
                <div><span class="text-slate-500">Gender:</span> {{ resident.gender }}</div>
                <div><span class="text-slate-500">Age:</span> {{ resident.age ?? '—' }}</div>
                <div><span class="text-slate-500">Phone:</span> {{ resident.phone || '—' }}</div>
                <div><span class="text-slate-500">{{ t('income') }}:</span> {{ resident.income_level || '—' }}</div>
                <div><span class="text-slate-500">{{ t('zakat') }}:</span> {{ resident.zakat_status }}</div>
                <div><span class="text-slate-500">Employment:</span> {{ resident.employment_sector || '—' }} ({{ resident.employment_status || '—' }})</div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <span v-if="resident.is_donation_giver_eligible" class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">{{ t('donation_givers') }}</span>
                <span v-if="resident.is_donation_receiver_eligible" class="px-2 py-1 bg-amber-100 text-amber-800 rounded text-xs">{{ t('donation_receivers') }}</span>
                <span v-if="resident.is_widow" class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">{{ t('widow') }}</span>
                <span v-if="resident.is_orphan" class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">{{ t('orphan') }}</span>
                <span v-if="resident.has_disability" class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">{{ t('disability') }}</span>
                <span v-if="resident.needs_urgent_aid" class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">{{ t('urgent_aid') }}</span>
            </div>

            <div v-if="resident.profession_categories?.length" class="mt-4">
                <p class="text-sm text-slate-500 mb-1">{{ t('profession') }}</p>
                <span v-for="p in resident.profession_categories" :key="p.id" class="inline-block px-2 py-1 bg-slate-100 rounded text-sm mr-1">{{ profLabel(p) }}</span>
            </div>
        </div>
    </AppLayout>
</template>
