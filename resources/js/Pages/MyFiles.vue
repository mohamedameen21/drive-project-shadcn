<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({
    layout: AuthenticatedLayout,
});
</script>

<template>
    <div class="py-12">
        <div class="max-w-[90rem] mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
            >
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ $page.url }}
                </div>
            </div>
        </div>
    </div>
</template>

<style></style>
