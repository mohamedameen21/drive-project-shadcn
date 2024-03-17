<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import Input from "@/shadcn/ui/input/Input.vue";
import { useColorMode } from "@vueuse/core";
import { ref } from "vue";
const mode = useColorMode();

const search = ref("");
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!--        <template #header>-->
        <!--            <h2-->
        <!--                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"-->
        <!--            >-->
        <!--                Dashboard-->
        <!--            </h2>-->
        <!--        </template>-->

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <Input
                            placeholder="Search for Files and Folders"
                            class="py-6 text-base"
                            v-model="search"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
