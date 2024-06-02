<script lang="ts" setup>
import { onMounted, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Dark from "@/Pages/Dark.vue";
import { emitter, FILE_UPLOAD_STARTED } from "@/event-bus";
import CustomSearch from "@/Components/CustomSearch.vue";
import CreateNewDropdown from "@/Components/CreateNewDropdown.vue";
import Navigation from "@/Components/Navigation.vue";
import FormProgress from "@/Components/FormProgress.vue";
import { useToast } from "@/shadcn/ui/toast";

const page = usePage();
const { toast } = useToast();
const fileUploadForm = useForm({
    files: [],
    relative_paths: [], // for subdirectory structure on Upload folder option
    parent_id: null,
});

const showingNavigationDropdown = ref(false);

const dragOver = ref(false);

onMounted(() => {
    emitter.on(FILE_UPLOAD_STARTED, uploadFiles);
});

const uploadFiles = (files) => {
    fileUploadForm.parent_id = page.props.folder.id;
    fileUploadForm.files = files;

    //The webkitRelativePath will have the subdirectory structure of the files
    // As the File object will only give the idea only on the file name & type etc not path.
    // Then we are slicing the first 20 files path.
    // the Files are automatically skipped by the php as the default config of the file upload limit is 20 files per request
    fileUploadForm.relative_paths = [...files]
        .map((file) => file.webkitRelativePath)
        .slice(0, 20);

    fileUploadForm.post(route("file.store"), {
        onError: (errors) => {
            console.log(errors);
            let message = "";
            if (Object.keys(errors).length > 0) {
                message = errors[Object.keys(errors)[0]];
            } else {
                message =
                    "Error occurred while uploading files. Please try again later.";
            }
            toast({
                description: message,
                variant: "error",
            });
        },
        onFinish: () => {
            fileUploadForm.clearErrors();
            fileUploadForm.reset();
        },
    });
};

const onDragOver = (event) => {
    dragOver.value = true;
};

const onDragLeave = (event) => {
    dragOver.value = false;
};

const handleDrop = (event) => {
    dragOver.value = false;
    const files = event.dataTransfer.files;

    if (!files.length) {
        return;
    }

    uploadFiles(files);
};
</script>

<template>
    <div>
        <Dark />

        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <!--          Navigation -->
            <Navigation
                :showingNavigationDropdown="showingNavigationDropdown"
            />

            <!-- Page Content -->
            <main
                :class="dragOver ? 'h-screen box-border' : ''"
                @drop.prevent="handleDrop"
                @dragover.prevent="onDragOver"
                @dragleave.prevent="onDragLeave"
            >
                <template v-if="dragOver">
                    <header
                        class="border-dashed border-2 border-gray-400 h-full p-12 flex"
                    >
                        <p
                            class="font-semibold text-gray-900 dark:text-white w-fit h-fit m-auto"
                        >
                            Drag & Drop Your Files Here
                        </p>
                    </header>
                </template>

                <template v-else>
                    <div class="py-12">
                        <div class="max-w-[90rem] mx-auto sm:px-6 lg:px-8">
                            <div
                                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                            >
                                <div
                                    class="p-6 text-gray-900 dark:text-gray-100"
                                >
                                    <main
                                        class="flex flex-col gap-6 justify-center items-center sm:flex-row"
                                    >
                                        <!--                      Dropdown button-->
                                        <CreateNewDropdown />

                                        <!--                      Search component-->
                                        <CustomSearch />
                                    </main>

                                    <slot />
                                </div>
                            </div>
                        </div>
                    </div>

                    <FormProgress :form="fileUploadForm" />
                </template>
            </main>
        </div>
    </div>
</template>
