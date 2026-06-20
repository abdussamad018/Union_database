<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ users: Object, roles: Array, houses: Array });
const form = useForm({ name: '', name_bn: '', email: '', password: '', role: 'bari_representative', house_id: '', locale: 'bn' });
const submit = () => form.post(route('admin.users.store'), { onSuccess: () => form.reset() });
</script>

<template>
    <Head title="Users" />
    <AppLayout>
        <h1 class="text-2xl font-bold mb-6">Users</h1>
        <form @submit.prevent="submit" class="bg-white rounded-xl border p-4 mb-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <input v-model="form.name" placeholder="Name" class="rounded-lg border-slate-300 text-sm" required />
            <input v-model="form.email" type="email" placeholder="Email" class="rounded-lg border-slate-300 text-sm" required />
            <input v-model="form.password" type="password" placeholder="Password" class="rounded-lg border-slate-300 text-sm" required />
            <select v-model="form.role" class="rounded-lg border-slate-300 text-sm"><option v-for="r in roles" :key="r.value" :value="r.value">{{ r.label }}</option></select>
            <button type="submit" class="px-4 py-2 bg-primary-700 text-white rounded-lg text-sm">Add User</button>
        </form>
        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50"><tr>
                    <th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Role</th><th class="px-4 py-3 text-left">Active</th>
                </tr></thead>
                <tbody>
                    <tr v-for="u in users.data" :key="u.id" class="border-t">
                        <td class="px-4 py-3">{{ u.name }}</td>
                        <td class="px-4 py-3">{{ u.email }}</td>
                        <td class="px-4 py-3">{{ u.role }}</td>
                        <td class="px-4 py-3">{{ u.is_active ? 'Yes' : 'No' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
