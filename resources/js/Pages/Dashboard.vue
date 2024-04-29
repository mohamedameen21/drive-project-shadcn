<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { useColorMode } from "@vueuse/core";
import CreateNewDropdown from "@/Components/CreateNewDropdown.vue";
import CustomeSearch from "@/Components/CustomeSearch.vue";
import Dark from "@/Pages/Dark.vue";

const mode = useColorMode();
defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Dashboard" />
</template>
