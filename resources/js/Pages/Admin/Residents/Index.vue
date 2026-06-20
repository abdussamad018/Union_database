<script setup>
import ResidentsIndex from '@/Pages/Residents/Index.vue';
defineProps({ residents: Object, filters: Object, villages: Object });
</script>
<template><ResidentsIndex :residents="residents" :filters="filters" :is-admin="true" /></template>
