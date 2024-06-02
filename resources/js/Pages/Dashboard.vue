<script lang="ts" setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { useColorMode } from "@vueuse/core";

const mode = useColorMode();
defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Dashboard" />
</template>
