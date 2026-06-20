<script setup>
defineProps({ label: String, value: [String, Number], icon: String, color: { type: String, default: 'primary' } });
</script>

<template>
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition">
        <p class="text-sm text-slate-500 font-medium">{{ label }}</p>
        <p :class="['text-2xl font-bold mt-1', color === 'accent' ? 'text-accent-600' : 'text-primary-700']">{{ value ?? 0 }}</p>
    </div>
</template>
