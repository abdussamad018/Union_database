<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';

const props = defineProps({
    residents: Object,
    villages: Array,
    professionCategories: Array,
    filters: Object,
    summary: Object,
    presets: Array,
});

const { t, locale } = useI18n();

const form = reactive({
    search: props.filters.search || '',
    village_id: props.filters.village_id || '',
    zakat_status: props.filters.zakat_status || '',
    is_donation_giver_eligible: props.filters.is_donation_giver_eligible || '',
    is_donation_receiver_eligible: props.filters.is_donation_receiver_eligible || '',
    needs_urgent_aid: props.filters.needs_urgent_aid || '',
    aid_priority: props.filters.aid_priority || '',
    employment_sector: props.filters.employment_sector || '',
    income_level: props.filters.income_level || '',
    is_widow: props.filters.is_widow || '',
    is_orphan: props.filters.is_orphan || '',
    has_disability: props.filters.has_disability || '',
    profession_category_id: props.filters.profession_category_id || '',
    preset: props.filters.preset || '',
});

const applyFilters = () => {
    const params = {};
    Object.entries(form).forEach(([k, v]) => { if (v) params[k] = v; });
    router.get(route('residents.viewer.index'), params, { preserveState: true, replace: true });
};

const applyPreset = (key) => {
    Object.keys(form).forEach(k => form[k] = '');
    form.preset = key;
    applyFilters();
};

const clearFilters = () => {
    Object.keys(form).forEach(k => form[k] = '');
    router.get(route('residents.viewer.index'));
};

const presetLabel = (p) => locale.value === 'bn' ? p.label_bn : p.label_en;
const profLabel = (p) => locale.value === 'bn' ? p.name_bn : p.name_en;

const zakatLabel = (s) => ({ pays: locale.value === 'bn' ? 'দেন' : 'Pays', does_not_pay: locale.value === 'bn' ? 'দেন না' : 'Does not pay', not_applicable: locale.value === 'bn' ? 'প্রযোজ্য নয়' : 'N/A' }[s] || s);
</script>

<template>
    <Head :title="t('residents')" />
    <AppLayout>
        <h1 class="text-2xl font-bold text-slate-900 mb-6">{{ t('residents') }}</h1>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <StatCard :label="t('zakat_payers')" :value="summary.zakat_payers" />
            <StatCard :label="t('zakat_receivers')" :value="summary.zakat_receivers" color="accent" />
            <StatCard :label="t('donation_givers')" :value="summary.donation_givers" />
            <StatCard :label="t('donation_receivers')" :value="summary.donation_receivers" color="accent" />
        </div>

        <div class="flex flex-wrap gap-2 mb-4">
            <button v-for="p in presets" :key="p.key" @click="applyPreset(p.key)"
                :class="['px-3 py-1.5 rounded-lg text-sm font-medium transition', form.preset === p.key ? 'bg-primary-700 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50']">
                {{ presetLabel(p) }}
            </button>
        </div>

        <div class="bg-white rounded-xl border p-4 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                <input v-model="form.search" @keyup.enter="applyFilters" :placeholder="t('search')" class="rounded-lg border-slate-300 text-sm" />
                <select v-model="form.village_id" class="rounded-lg border-slate-300 text-sm">
                    <option value="">{{ t('all_villages') }}</option>
                    <option v-for="v in villages" :key="v.id" :value="v.id">Ward {{ v.ward_number }} — {{ v.name_bn }}</option>
                </select>
                <select v-model="form.zakat_status" class="rounded-lg border-slate-300 text-sm">
                    <option value="">{{ t('all_zakat') }}</option>
                    <option value="pays">{{ t('zakat_pays') }}</option>
                    <option value="does_not_pay">{{ t('zakat_does_not_pay') }}</option>
                    <option value="not_applicable">{{ t('zakat_na') }}</option>
                </select>
                <select v-model="form.income_level" class="rounded-lg border-slate-300 text-sm">
                    <option value="">{{ t('all_income') }}</option>
                    <option value="very_low">Very Low</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
                <select v-model="form.employment_sector" class="rounded-lg border-slate-300 text-sm">
                    <option value="">{{ t('all_employment') }}</option>
                    <option value="govt_job_holder">Govt Job</option>
                    <option value="private_job_holder">Private Job</option>
                    <option value="self_employed">Self Employed</option>
                    <option value="business">Business</option>
                    <option value="agriculture">Agriculture</option>
                </select>
                <select v-model="form.profession_category_id" class="rounded-lg border-slate-300 text-sm">
                    <option value="">{{ t('all_professions') }}</option>
                    <option v-for="p in professionCategories" :key="p.id" :value="p.id">{{ profLabel(p) }}</option>
                </select>
                <select v-model="form.aid_priority" class="rounded-lg border-slate-300 text-sm">
                    <option value="">{{ t('all_priority') }}</option>
                    <option value="critical">Critical</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="normal">Normal</option>
                </select>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" v-model="form.is_donation_giver_eligible" true-value="1" false-value="" class="rounded" /> {{ t('donation_givers') }}</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" v-model="form.is_donation_receiver_eligible" true-value="1" false-value="" class="rounded" /> {{ t('donation_receivers') }}</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" v-model="form.is_widow" true-value="1" false-value="" class="rounded" /> {{ t('widow') }}</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" v-model="form.is_orphan" true-value="1" false-value="" class="rounded" /> {{ t('orphan') }}</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" v-model="form.has_disability" true-value="1" false-value="" class="rounded" /> {{ t('disability') }}</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" v-model="form.needs_urgent_aid" true-value="1" false-value="" class="rounded" /> {{ t('urgent_aid') }}</label>
            </div>
            <div class="flex gap-2 mt-3">
                <button @click="applyFilters" class="px-4 py-2 bg-primary-700 text-white rounded-lg text-sm font-medium">{{ t('search') }}</button>
                <button @click="clearFilters" class="px-4 py-2 border rounded-lg text-sm text-slate-600 hover:bg-slate-50">{{ t('clear_filters') }}</button>
            </div>
        </div>

        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left">
                    <tr>
                        <th class="px-4 py-3">{{ t('name_bn') }}</th>
                        <th class="px-4 py-3">{{ t('village') }}</th>
                        <th class="px-4 py-3">{{ t('profession') }}</th>
                        <th class="px-4 py-3">{{ t('zakat') }}</th>
                        <th class="px-4 py-3">{{ t('income') }}</th>
                        <th class="px-4 py-3">{{ t('charity_status') }}</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="r in residents.data" :key="r.id" class="border-t hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ r.name_bn }}</p>
                            <p v-if="r.father_name" class="text-xs text-slate-500">{{ r.father_name }}</p>
                        </td>
                        <td class="px-4 py-3">{{ r.village?.name_bn }}<br><span class="text-xs text-slate-500">{{ r.house_name }}</span></td>
                        <td class="px-4 py-3">
                            <span v-for="p in r.profession_categories" :key="p.id" class="inline-block px-1.5 py-0.5 bg-slate-100 rounded text-xs mr-1">{{ profLabel(p) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span :class="['px-2 py-0.5 rounded text-xs', r.zakat_status === 'pays' ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600']">{{ zakatLabel(r.zakat_status) }}</span>
                        </td>
                        <td class="px-4 py-3">{{ r.income_level || '—' }}</td>
                        <td class="px-4 py-3">
                            <span v-if="r.is_donation_giver_eligible" class="text-xs text-green-700">Giver</span>
                            <span v-if="r.is_donation_receiver_eligible" class="text-xs text-amber-700 ml-1">Receiver</span>
                            <span v-if="r.needs_urgent_aid" class="text-xs text-red-600 ml-1">Urgent</span>
                        </td>
                        <td class="px-4 py-3"><Link :href="route('residents.viewer.show', r.id)" class="text-primary-700 hover:underline">{{ t('view') }}</Link></td>
                    </tr>
                </tbody>
            </table>
            <div v-if="!residents.data?.length" class="p-8 text-center text-slate-500">{{ t('no_results') }}</div>
        </div>

        <div v-if="residents.links?.length > 3" class="flex gap-1 mt-4 flex-wrap">
            <Link v-for="link in residents.links" :key="link.label" :href="link.url" v-html="link.label"
                :class="['px-3 py-1 rounded text-sm', link.active ? 'bg-primary-700 text-white' : 'bg-white border text-slate-600', !link.url ? 'opacity-50 pointer-events-none' : '']" />
        </div>
    </AppLayout>
</template>
