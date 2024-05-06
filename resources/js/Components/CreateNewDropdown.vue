<script setup lang="ts">
import { CloudUpload, Folder, FolderPlus, Plus } from "lucide-vue-next";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/shadcn/ui/dropdown-menu";

import { Button } from "@/shadcn/ui/button";
import Input from "../shadcn/ui/input/Input.vue";
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { Toaster } from "@/shadcn/ui/toast";
import CreateNewFolderModal from "@/Components/CreateNewFolderModal.vue";

const page = usePage();

const openModal = ref(false);
const updateModal = (value) => {
    openModal.value = value;
};
</script>

<template>
    <Toaster />

    <div class="w-full sm:max-w-[400px]">
        <DropdownMenu class="w-72 sm:max-w-[400px]">
            <DropdownMenuTrigger class="w-72 sm:max-w-[400px]" as-child>
                <Button class="w-full py-6" variant="outline">
                    <Plus class="mr-2 h-4 w-4" />
                    Create New
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent class="w-72 sm:max-w-[400px]">
                <DropdownMenuLabel>Create New</DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuGroup>
                    <DropdownMenuItem @click="openModal = true">
                        <button class="w-full flex items-center">
                            <Folder class="mr-2 h-4 w-4" />
                            <span>New Folder</span>
                        </button>
                    </DropdownMenuItem>
                    <DropdownMenuItem>
                        <button class="w-full flex items-center relative">
                            <CloudUpload class="mr-2 h-4 w-4" />
                            <span>Upload File</span>
                            <input
                                type="file"
                                class="absolute left-0 right-0 top-0 bottom-0 cursor-pointer opacity-0 w-full h-full"
                            />
                        </button>
                    </DropdownMenuItem>
                    <DropdownMenuItem>
                        <FolderPlus class="mr-2 h-4 w-4" />
                        <span>Upload Folder</span>
                    </DropdownMenuItem>
                </DropdownMenuGroup>
            </DropdownMenuContent>
        </DropdownMenu>
    </div>

    <div>
        <CreateNewFolderModal
            :openModal="openModal"
            @updateModal="updateModal"
        />
    </div>
</template>

<style scoped></style>
