<script setup>
import { computed, ref } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

const page = usePage();
const { t } = useI18n();
const sidebarOpen = ref(false);
const user = computed(() => page.props.auth.user);
const role = computed(() => user.value?.role);

const adminLinks = [
    { name: 'dashboard', route: 'admin.dashboard', label: 'dashboard' },
    { name: 'quick', route: 'admin.quick-decision', label: 'quick_decision' },
    { name: 'residents', route: 'admin.residents.index', label: 'residents' },
    { name: 'houses', route: 'admin.houses.index', label: 'houses' },
    { name: 'facilities', route: 'admin.facilities.index', label: 'facilities' },
    { name: 'donations', route: 'admin.donations.index', label: 'donations' },
    { name: 'users', route: 'admin.users.index', label: 'users' },
    { name: 'custom', route: 'admin.custom-fields.index', label: 'custom_fields' },
    { name: 'logs', route: 'admin.activity-logs', label: 'activity_logs' },
];

const bariLinks = [
    { name: 'dashboard', route: 'bari.dashboard', label: 'dashboard' },
    { name: 'residents', route: 'bari.residents.index', label: 'residents' },
];

const viewerLinks = [
    { name: 'dashboard', route: 'viewer.dashboard', label: 'dashboard' },
    { name: 'donations', route: 'donations.index', label: 'donations' },
    { name: 'residents', route: 'residents.viewer.index', label: 'residents' },
];

const links = computed(() => {
    if (role.value === 'super_admin') return adminLinks;
    if (role.value === 'bari_representative') return bariLinks;
    return viewerLinks;
});

const isActive = (routeName) => route().current(routeName) || route().current(routeName.replace('.index', '.*'));
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <FlashMessage />
        <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-black/40 lg:hidden" @click="sidebarOpen = false" />

        <aside :class="['fixed inset-y-0 left-0 z-50 w-64 bg-primary-800 text-white transform transition-transform lg:translate-x-0', sidebarOpen ? 'translate-x-0' : '-translate-x-full']">
            <div class="p-6 border-b border-primary-700">
                <Link :href="route('landing')" class="text-xl font-bold">{{ t('app_name') }}</Link>
                <p class="text-primary-200 text-sm mt-1">{{ user?.role_label }}</p>
            </div>
            <nav class="p-4 space-y-1">
                <Link v-for="link in links" :key="link.name" :href="route(link.route)"
                    :class="['block px-4 py-2.5 rounded-lg text-sm font-medium transition', isActive(link.route) ? 'bg-primary-700 text-white' : 'text-primary-100 hover:bg-primary-700/60']">
                    {{ t(link.label) }}
                </Link>
            </nav>
        </aside>

        <div class="lg:pl-64">
            <header class="sticky top-0 z-30 bg-white border-b border-slate-200 px-4 sm:px-6 h-16 flex items-center justify-between">
                <button class="lg:hidden p-2 rounded-lg hover:bg-slate-100" @click="sidebarOpen = !sidebarOpen">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-4 ml-auto">
                    <LanguageSwitcher />
                    <span class="text-sm text-slate-600 hidden sm:block">{{ user?.name }}</span>
                    <Link :href="route('profile.edit')" class="text-sm text-primary-700 hover:underline">{{ t('profile') }}</Link>
                    <Link :href="route('logout')" method="post" as="button" class="text-sm text-red-600 hover:underline">{{ t('logout') }}</Link>
                </div>
            </header>
            <main class="p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
