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

    <div class="py-12">
        <div class="max-w-[90rem] mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
            >
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <main
                        class="flex flex-col gap-6 justify-center items-center sm:flex-row"
                    >
                        <!--                      Dropdown button-->
                        <CreateNewDropdown />

                        <!--                      Search component-->
                        <CustomeSearch />
                    </main>
                </div>
            </div>
        </div>
    </div>
</template>
