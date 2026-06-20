<script setup>
import ResidentsIndex from '@/Pages/Residents/Index.vue';
defineProps({ residents: Object, filters: Object });
</script>
<template><ResidentsIndex :residents="residents" :filters="filters" /></template>
