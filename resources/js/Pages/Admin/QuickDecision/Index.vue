<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ residents: Array, villages: Array });
const { t, locale } = useI18n();

const form = useForm({ resident_id: '', amount: '', description_bn: '', category: 'charity' });
const submit = (id) => {
    form.resident_id = id;
    form.post(route('admin.assign-aid'));
};
</script>

<template>
    <Head :title="t('quick_decision')" />
    <AppLayout>
        <h1 class="text-2xl font-bold mb-6">{{ t('quick_decision') }}</h1>
        <div class="space-y-4">
            <div v-for="r in residents" :key="r.id" class="bg-white rounded-xl border p-5 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="font-semibold text-slate-900">{{ r.name_bn }}</h3>
                    <p class="text-sm text-slate-500">{{ r.house?.village?.name_bn }} — {{ r.house?.house_name }}</p>
                    <div class="flex gap-2 mt-2">
                        <span class="px-2 py-0.5 bg-primary-100 text-primary-800 rounded text-xs">{{ t('vulnerability_score') }}: {{ r.vulnerability_score }}</span>
                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded text-xs">{{ r.aid_priority }}</span>
                        <span v-if="r.recent_aid" class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">{{ t('recent_aid_warning') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="number" v-model="form.amount" placeholder="Amount" class="w-32 rounded-lg border-slate-300 text-sm" />
                    <button @click="submit(r.id)" class="px-4 py-2 bg-accent-500 text-white rounded-lg text-sm font-medium hover:bg-accent-600">{{ t('assign_aid') }}</button>
                </div>
            </div>
            <div v-if="!residents?.length" class="text-center text-slate-500 py-12">No eligible residents in queue.</div>
        </div>
    </AppLayout>
</template>
