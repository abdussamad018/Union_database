<script setup>
import { reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    villages: Array,
    houses: Array,
    filters: Object,
    routeName: { type: String, required: true },
    stats: Object,
});

const { t, locale } = useI18n();

const form = reactive({
    village_id: props.filters?.village_id || '',
    house_id: props.filters?.house_id || '',
});

const filteredHouses = computed(() => {
    if (!form.village_id) return [];
    return props.houses.filter((h) => String(h.village_id) === String(form.village_id));
});

const scopeLabel = computed(() => {
    if (!props.stats?.is_filtered) return null;
    return locale.value === 'bn' ? props.stats.scope_label_bn : props.stats.scope_label_en;
});

const applyFilters = () => {
    const params = {};
    if (form.village_id) params.village_id = form.village_id;
    if (form.house_id) params.house_id = form.house_id;
    router.get(route(props.routeName), params, { preserveState: true, replace: true });
};

const onVillageChange = () => {
    form.house_id = '';
    applyFilters();
};

const onHouseChange = () => applyFilters();

const clearFilters = () => {
    form.village_id = '';
    form.house_id = '';
    router.get(route(props.routeName));
};
</script>

<template>
    <div class="bg-white rounded-xl border p-4 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
            <h2 class="text-sm font-semibold text-slate-700">{{ t('filter_dashboard') }}</h2>
            <p v-if="scopeLabel" class="text-sm text-primary-700 font-medium">{{ t('showing_data_for') }}: {{ scopeLabel }}</p>
        </div>
        <div class="flex flex-wrap gap-3 items-end">
            <select v-model="form.village_id" @change="onVillageChange" class="rounded-lg border-slate-300 text-sm min-w-[180px]">
                <option value="">{{ t('all_villages') }}</option>
                <option v-for="v in villages" :key="v.id" :value="v.id">Ward {{ v.ward_number }} — {{ v.name_bn }}</option>
            </select>
            <select v-model="form.house_id" @change="onHouseChange" :disabled="!form.village_id" class="rounded-lg border-slate-300 text-sm min-w-[180px] disabled:bg-slate-100 disabled:text-slate-400">
                <option value="">{{ form.village_id ? t('all_houses') : t('select_ward_first') }}</option>
                <option v-for="h in filteredHouses" :key="h.id" :value="h.id">{{ h.house_name }}</option>
            </select>
            <button v-if="stats?.is_filtered" @click="clearFilters" class="px-4 py-2 border rounded-lg text-sm text-slate-600 hover:bg-slate-50">{{ t('clear_filters') }}</button>
        </div>
    </div>
</template>
