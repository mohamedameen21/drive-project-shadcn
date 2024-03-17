<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import Input from "@/shadcn/ui/input/Input.vue";
import { useColorMode } from "@vueuse/core";
import { ref } from "vue";

import { Button } from "@/shadcn/ui/button";
import { Search, Folder, FolderPlus, CloudUpload, Plus } from "lucide-vue-next";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/shadcn/ui/dropdown-menu";

const mode = useColorMode();
const searchedTerm = ref("");
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
                        <div class="w-full sm:max-w-sm">
                            <DropdownMenu class="w-72 sm:w-96">
                                <DropdownMenuTrigger
                                    class="w-72 sm:w-96"
                                    as-child
                                >
                                    <Button
                                        class="w-full py-6"
                                        variant="outline"
                                    >
                                        <Plus class="mr-2 h-4 w-4" />
                                        Create New</Button
                                    >
                                </DropdownMenuTrigger>
                                <DropdownMenuContent class="w-72 sm:w-96">
                                    <DropdownMenuLabel
                                        >Create New
                                    </DropdownMenuLabel>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuGroup>
                                        <DropdownMenuItem>
                                            <Folder class="mr-2 h-4 w-4" />
                                            <span>New Folder</span>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>
                                            <CloudUpload class="mr-2 h-4 w-4" />
                                            <span>Upload File</span>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>
                                            <FolderPlus class="mr-2 h-4 w-4" />
                                            <span>Upload Folder</span>
                                        </DropdownMenuItem>
                                    </DropdownMenuGroup>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>

                        <!--                      Search component-->
                        <div class="relative w-full items-center">
                            <Input
                                id="search"
                                type="text"
                                placeholder="Search for files and folder"
                                class="pl-12 py-6 text-base focus:outline-none focus:border-0"
                            />
                            <span
                                class="absolute start-0 inset-y-0 flex items-center justify-center px-2"
                            >
                                <Search class="size-6 text-muted-foreground" />
                            </span>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
</template>
