<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps({ canResetPassword: Boolean, status: String });
const { t } = useI18n();
const form = useForm({ email: '', password: '', remember: false });
const submit = () => form.post(route('login'), { onFinish: () => form.reset('password') });
</script>

<template>
    <GuestLayout>
        <Head :title="t('login')" />
        <div class="flex justify-end mb-4"><LanguageSwitcher /></div>
        <div class="mb-6 text-center">
            <Link :href="route('landing')" class="text-2xl font-bold text-primary-700">{{ t('app_name') }}</Link>
            <p class="text-sm text-slate-500 mt-1">admin@union.test / password</p>
        </div>
        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">{{ status }}</div>
        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div>
                <InputLabel for="password" value="Password" />
                <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center"><Checkbox name="remember" v-model:checked="form.remember" /><span class="ms-2 text-sm text-gray-600">Remember me</span></label>
                <PrimaryButton :disabled="form.processing">{{ t('login') }}</PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
