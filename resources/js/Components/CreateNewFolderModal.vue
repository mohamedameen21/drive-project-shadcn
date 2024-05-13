<script lang="ts" setup>
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/shadcn/ui/dialog";
import InputError from "@/Components/InputError.vue";
import { Button } from "@/shadcn/ui/button";
import { DialogClose } from "radix-vue";
import Input from "../shadcn/ui/input/Input.vue";
import { useToast } from "@/shadcn/ui/toast";
import { useForm, usePage } from "@inertiajs/vue3";
import { ref, toRef, watch } from "vue";

const { toast } = useToast();
const page = usePage();

const props = defineProps({
    openModal: {
        type: Boolean,
        required: true,
    },
});
const emit = defineEmits(["updateModal"]);

const form = useForm({
    name: "",
    parent_id: null,
});
const folderNameInput = ref(null);
const openModalRef = toRef(props, "openModal");

watch(openModalRef, (value) => {
    if (!value) {
        form.clearErrors();
        form.reset();
    }
});
const createFolder = () => {
    form.parent_id = page.props.folder.id;
    form.post(route("folder.store"), {
        preserveScroll: true,
        onSuccess: (response) => {
            toast({
                description: "Folder created successfully",
                variant: "success",
            });
            // emit updateModal with a value false
            emit("updateModal", false);
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
    <Dialog :open="openModalRef" @update:open="$emit('updateModal')">
        <DialogContent
            class="max-w-[300px] sm:max-w-[425px] md:max-w-[600px] rounded-lg"
        >
            <DialogDescription>
                Create a new folder in
                <strong>{{ page.props.folder.name }}</strong>
            </DialogDescription>
            <DialogHeader>
                <DialogTitle>Create New Folder</DialogTitle>
            </DialogHeader>
            <hr class="mb-4" />
            <div>
                <Input
                    id="folderName"
                    ref="folderNameInput"
                    v-model="form.name"
                    :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500':
                            form.errors.name,
                    }"
                    class="focus:outline-none focus:border-0 focus:outline-none w-full rounded-md text-gray-900 dark:text-gray-100"
                    placeholder="Folder Name"
                    type="text"
                    @keyup.enter="createFolder"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>
            <DialogFooter class="mt-3">
                <DialogClose>
                    <Button
                        class="w-full mt-3 sm:mt-0 sm:w-auto"
                        variant="secondary"
                        >Cancel
                    </Button>
                </DialogClose>
                <Button
                    :class="{
                        'opacity-25': form.processing,
                        'cursor-not-allowed': form.processing,
                    }"
                    :disabled="form.processing"
                    @click="createFolder"
                >
                    Create
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<style scoped></style>
