<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ donation: Object });
</script>

<template>
    <Head title="Donation Detail" />
    <AppLayout>
        <Link :href="route('donations.index')" class="text-primary-700 text-sm mb-4 inline-block">&larr; Back</Link>
        <div class="bg-white rounded-xl border p-6 max-w-lg">
            <h1 class="text-xl font-bold mb-4">{{ donation.type }} — {{ Number(donation.amount).toLocaleString() }} BDT</h1>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Date</dt><dd>{{ donation.date?.substring?.(0,10) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Category</dt><dd>{{ donation.category }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Name</dt><dd>{{ donation.donor_or_recipient_name }}</dd></div>
                <div v-if="donation.description_bn"><dt class="text-slate-500">Description</dt><dd>{{ donation.description_bn }}</dd></div>
            </dl>
        </div>
    </AppLayout>
</template>
