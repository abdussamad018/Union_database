<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ facilities: Object, facilityTypes: Array, villages: Array });
const form = useForm({ facility_type_id: '', village_id: '', name_bn: '', name_en: '', address: '', contact_phone: '' });
const submit = () => form.post(route('admin.facilities.store'), { onSuccess: () => form.reset() });
</script>

<template>
    <Head title="Facilities" />
    <AppLayout>
        <h1 class="text-2xl font-bold mb-6">Facilities</h1>
        <form @submit.prevent="submit" class="bg-white rounded-xl border p-4 mb-6 grid grid-cols-2 md:grid-cols-3 gap-3">
            <select v-model="form.facility_type_id" class="rounded-lg border-slate-300 text-sm" required>
                <option v-for="ft in facilityTypes" :key="ft.id" :value="ft.id">{{ ft.name_bn }}</option>
            </select>
            <select v-model="form.village_id" class="rounded-lg border-slate-300 text-sm">
                <option value="">Union-wide</option><option v-for="v in villages" :key="v.id" :value="v.id">{{ v.name_bn }}</option>
            </select>
            <input v-model="form.name_bn" placeholder="Name BN" class="rounded-lg border-slate-300 text-sm" required />
            <input v-model="form.name_en" placeholder="Name EN" class="rounded-lg border-slate-300 text-sm" />
            <input v-model="form.address" placeholder="Address" class="rounded-lg border-slate-300 text-sm" />
            <button type="submit" class="px-4 py-2 bg-primary-700 text-white rounded-lg text-sm">Add</button>
        </form>
        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50"><tr>
                    <th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-left">Village</th>
                </tr></thead>
                <tbody>
                    <tr v-for="f in facilities.data" :key="f.id" class="border-t">
                        <td class="px-4 py-3">{{ f.name_bn }}</td>
                        <td class="px-4 py-3">{{ f.facility_type?.name_bn }}</td>
                        <td class="px-4 py-3">{{ f.village?.name_bn || 'Union-wide' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
