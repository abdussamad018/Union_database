<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const { t } = useI18n();
</script>

<template>
    <Head :title="t('profile')" />

    <AppLayout>
        <h1 class="text-2xl font-bold text-slate-900 mb-6">{{ t('profile') }}</h1>

        <div class="max-w-3xl space-y-6">
            <div class="bg-white rounded-xl border p-6 sm:p-8">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div class="bg-white rounded-xl border p-6 sm:p-8">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <div class="bg-white rounded-xl border p-6 sm:p-8">
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </AppLayout>
</template>
