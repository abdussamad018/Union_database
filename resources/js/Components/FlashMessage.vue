<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const flash = computed(() => page.props.flash);
</script>

<template>
    <div v-if="flash?.success" class="fixed top-20 right-4 z-50 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm">{{ flash.success }}</div>
    <div v-if="flash?.error" class="fixed top-20 right-4 z-50 bg-red-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm">{{ flash.error }}</div>
</template>
