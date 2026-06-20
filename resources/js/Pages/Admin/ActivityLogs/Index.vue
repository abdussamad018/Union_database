<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ logs: Object });
</script>

<template>
    <Head title="Activity Logs" />
    <AppLayout>
        <h1 class="text-2xl font-bold mb-6">Activity Logs</h1>
        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50"><tr>
                    <th class="px-4 py-3 text-left">Time</th><th class="px-4 py-3 text-left">User</th>
                    <th class="px-4 py-3 text-left">Action</th><th class="px-4 py-3 text-left">Model</th>
                </tr></thead>
                <tbody>
                    <tr v-for="log in logs.data" :key="log.id" class="border-t">
                        <td class="px-4 py-3">{{ log.created_at }}</td>
                        <td class="px-4 py-3">{{ log.user?.name || 'System' }}</td>
                        <td class="px-4 py-3">{{ log.action }}</td>
                        <td class="px-4 py-3">{{ log.model_type }} #{{ log.model_id }}</td>
                    </tr>
                </tbody>
            </table>
            <div v-if="!logs.data?.length" class="p-8 text-center text-slate-500">No activity logs yet.</div>
        </div>
    </AppLayout>
</template>
