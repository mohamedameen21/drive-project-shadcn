<script setup lang="ts">
import {
    CircleCheck,
    CloudUpload,
    Folder,
    FolderPlus,
    Plus,
} from "lucide-vue-next";
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

import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/shadcn/ui/dialog";
import { nextTick, ref, watch } from "vue";
import { DialogClose } from "radix-vue";
import { useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import { useToast } from "@/shadcn/ui/toast/use-toast.ts";
import { Toaster } from "@/shadcn/ui/toast";

const { toast } = useToast();

const openModal = ref(false);
const form = useForm({
    name: "",
});
const folderNameInput = ref(null);

watch(openModal, (value) => {
    if (!value) {
        form.clearErrors();
        form.reset();
    }
});

const createFolder = () => {
    form.post(route("folder.store"), {
        preserveScroll: true,
        onSuccess: (response) => {
            toast({
                description: "Folder created successfully",
                variant: "success",
            });
            openModal.value = false;
            form.reset();
        },
        onError: () => {
            toast({
                description: "Failed to create folder",
                variant: "error",
            });
        },
    });
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

    <div>
        <Dialog :open="openModal" v-model:open="openModal">
            <DialogContent
                class="max-w-[300px] sm:max-w-[425px] md:max-w-[600px] rounded-lg"
            >
                <DialogHeader>
                    <DialogTitle>Create New Folder</DialogTitle>
                </DialogHeader>
                <hr class="mb-4" />
                <div>
                    <Input
                        ref="folderNameInput"
                        id="folderName"
                        type="text"
                        placeholder="Folder Name"
                        v-model="form.name"
                        @keyup.enter="createFolder"
                        class="focus:outline-none focus:border-0 focus:outline-none w-full rounded-md text-gray-900 dark:text-gray-100"
                        :class="{
                            'border-red-500 focus:border-red-500 focus:ring-red-500':
                                form.errors.name,
                        }"
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>
                <DialogFooter class="mt-3">
                    <DialogClose>
                        <Button
                            variant="secondary"
                            class="w-full mt-3 sm:mt-0 sm:w-auto"
                            >Cancel
                        </Button>
                    </DialogClose>
                    <Button
                        :disabled="form.processing"
                        :class="{
                            'opacity-25': form.processing,
                            'cursor-not-allowed': form.processing,
                        }"
                        @click="createFolder"
                    >
                        Create
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>

<style scoped></style>
