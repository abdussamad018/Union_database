<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ houses: Object, villages: Array });
const form = useForm({ village_id: '', house_name: '', address: '', representative_user_id: '' });
const submit = () => form.post(route('admin.houses.store'), { onSuccess: () => form.reset() });
</script>

<template>
    <Head title="Houses" />
    <AppLayout>
        <h1 class="text-2xl font-bold mb-6">Houses</h1>
        <form @submit.prevent="submit" class="bg-white rounded-xl border p-4 mb-6 flex flex-wrap gap-3 items-end">
            <div><label class="text-xs text-slate-500">Village</label>
                <select v-model="form.village_id" class="rounded-lg border-slate-300 text-sm" required>
                    <option v-for="v in villages" :key="v.id" :value="v.id">Ward {{ v.ward_number }} — {{ v.name_bn }}</option>
                </select>
            </div>
            <div><label class="text-xs text-slate-500">House Name</label><input v-model="form.house_name" class="rounded-lg border-slate-300 text-sm" required /></div>
            <div><label class="text-xs text-slate-500">Address</label><input v-model="form.address" class="rounded-lg border-slate-300 text-sm" /></div>
            <button type="submit" class="px-4 py-2 bg-primary-700 text-white rounded-lg text-sm">Add House</button>
        </form>
        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50"><tr>
                    <th class="px-4 py-3 text-left">House</th><th class="px-4 py-3 text-left">Village</th>
                    <th class="px-4 py-3 text-left">Representative</th><th class="px-4 py-3 text-left">Residents</th>
                </tr></thead>
                <tbody>
                    <tr v-for="h in houses.data" :key="h.id" class="border-t">
                        <td class="px-4 py-3 font-medium">{{ h.house_name }}</td>
                        <td class="px-4 py-3">{{ h.village?.name_bn }}</td>
                        <td class="px-4 py-3">{{ h.representative?.name || '—' }}</td>
                        <td class="px-4 py-3">{{ h.residents_count }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
