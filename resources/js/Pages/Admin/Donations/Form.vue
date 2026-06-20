<script setup>
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ residents: Array });
const form = useForm({
    type: 'received', amount: '', date: new Date().toISOString().substring(0, 10),
    resident_id: '', description_bn: '', category: 'charity', donor_or_recipient_name: '',
});
const submit = () => form.post(route('admin.donations.store'));
</script>

<template>
    <Head title="Add Donation" />
    <AppLayout>
        <h1 class="text-2xl font-bold mb-6">Record Donation</h1>
        <form @submit.prevent="submit" class="bg-white rounded-xl border p-6 max-w-lg space-y-4">
            <div><label class="text-sm font-medium">Type</label>
                <select v-model="form.type" class="w-full rounded-lg border-slate-300"><option value="given">Given</option><option value="received">Received</option></select>
            </div>
            <div><label class="text-sm font-medium">Amount</label><input type="number" v-model="form.amount" class="w-full rounded-lg border-slate-300" required /></div>
            <div><label class="text-sm font-medium">Date</label><input type="date" v-model="form.date" class="w-full rounded-lg border-slate-300" required /></div>
            <div><label class="text-sm font-medium">Resident</label>
                <select v-model="form.resident_id" class="w-full rounded-lg border-slate-300"><option value="">—</option><option v-for="r in residents" :key="r.id" :value="r.id">{{ r.name_bn }}</option></select>
            </div>
            <div><label class="text-sm font-medium">Name</label><input v-model="form.donor_or_recipient_name" class="w-full rounded-lg border-slate-300" /></div>
            <div><label class="text-sm font-medium">Category</label>
                <select v-model="form.category" class="w-full rounded-lg border-slate-300"><option value="charity">Charity</option><option value="education">Education</option><option value="health">Health</option></select>
            </div>
            <button type="submit" class="px-6 py-2 bg-primary-700 text-white rounded-lg" :disabled="form.processing">Save</button>
        </form>
    </AppLayout>
</template>
