<script lang="ts" setup>
import { Folder, Plus } from "lucide-vue-next";
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
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { Toaster } from "@/shadcn/ui/toast";
import CreateNewFolderModal from "@/Components/CreateNewFolderModal.vue";
import FileUploadMenuItem from "@/Components/FileUploadMenuItem.vue";
import FolderUploadMenuItem from "@/Components/FolderUploadMenuItem.vue";

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
            <DropdownMenuTrigger as-child class="w-72 sm:max-w-[400px]">
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
                        <FileUploadMenuItem />
                    </DropdownMenuItem>
                    <DropdownMenuItem>
                        <FolderUploadMenuItem />
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
