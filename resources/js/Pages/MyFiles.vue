<script lang="ts" setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, router} from "@inertiajs/vue3";
import FilesBreadCrumb from "@/Components/FilesBreadCrumb.vue";
import getFileType from "@/helpers/getFileType";
import {
    FileArchive,
    FileAudio,
    FileCheck,
    FileSpreadsheet,
    FileText,
    FileType,
    Folder,
    Image,
    Minus,
    Presentation,
    Video,
    FileQuestion,
    ArrowDownToLine,
    Trash,
} from "lucide-vue-next";
import { computed, onMounted, ref, watch, watchEffect } from "vue";
import axios from "axios";
import { Checkbox } from "@/shadcn/ui/checkbox";
import { vOnLongPress } from "@vueuse/components";
import { toast } from "@/shadcn/ui/toast";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/shadcn/ui/alert-dialog";
import { Button } from "@/shadcn/ui/button";

defineOptions({
    layout: AuthenticatedLayout,
});

const props = defineProps({
    files: Object,
    folder: Object,
    ancestors: Object,
    errors: Object, // default props that is passed from inertia to every components
    auth: Object, // default props that is passed from inertia to every components
    flash: Object, // default props that is passed from inertia to every components
});

const loadMoreRecordsContainerElement = ref();
const allFiles = ref({
    data: props.files.data,
    next: props.files.links.next,
});
const selectedFiles = ref({});
const isAllFilesSelected = ref(false);
const lastSelectedFile = ref(null);
const longPressedDirective = ref(false);

const selectedIds = computed(() => {
    return Object.entries(selectedFiles.value)
        .filter(([key, value]) => value)
        .map(([key, value]) => key);
});

const isMobile = computed(() => {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
        navigator.userAgent,
    );
});
const openFolder = (file) => {
    if (!file.is_folder) return;

    router.visit(route("myFiles", { folderPath: file.path }));
};

const toggleCheckbox = (selectedFile, event) => {
    if (!event) {
        selectedFiles.value[selectedFile.id] =
            !selectedFiles.value[selectedFile.id];
        return;
    }

    // Select a block when shift key is pressed
    if (event.shiftKey && lastSelectedFile.value !== null) {
        const start = Math.min(
            allFiles.value.data.indexOf(selectedFile),
            allFiles.value.data.indexOf(lastSelectedFile.value),
        );
        const end = Math.max(
            allFiles.value.data.indexOf(selectedFile),
            allFiles.value.data.indexOf(lastSelectedFile.value),
        );
        allFiles.value.data.slice(start, end + 1).forEach((currentFile) => {
            selectedFiles.value[currentFile.id] = true;
        });
    } else {
        selectedFiles.value[selectedFile.id] =
            !selectedFiles.value[selectedFile.id];
    }

    lastSelectedFile.value = selectedFile;
};

const fileIconMapper = {
    folder: Folder,
    zip: FileArchive,
    video: Video,
    audio: FileAudio,
    img: Image,
    txt: FileText,
    ppt: Presentation,
    xls: FileSpreadsheet,
    pdf: FileCheck,
    doc: FileType,
    unknown: FileQuestion,
};

const lodeMoreContent = async () => {
    if (allFiles.value.next) {
        // convert this in async await
        const response = await axios.get(allFiles.value.next);
        allFiles.value.data = [...allFiles.value.data, ...response.data.data];
        allFiles.value.next = response.data.links.next;
        allFiles.value.data.forEach((file) => {
            selectedFiles.value[file.id] = isAllFilesSelected.value;
        });
    }
};

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    lodeMoreContent();
                }
            });
        },
        {
            rootMargin: "100px",
        },
    );

    observer.observe(loadMoreRecordsContainerElement.value);
});

function onLongPressCallbackDirective(file, e: PointerEvent) {
    longPressedDirective.value = true;
    toggleCheckbox(file, null);
}

function resetDirective() {
    longPressedDirective.value = false;
}

const isTriggeredFromWatchEffect = ref(false);

watch(isAllFilesSelected, () => {
    if (isTriggeredFromWatchEffect.value) return;

    allFiles.value.data.forEach((file) => {
        selectedFiles.value[file.id] = isAllFilesSelected.value;
    });
});

watchEffect(() => {
    // All files are not selected
    if (
        Object.values(allFiles.value.data).every(
            (file) => !selectedFiles.value[file.id],
        )
    ) {
        resetDirective();
        isTriggeredFromWatchEffect.value = true;
        isAllFilesSelected.value = false;

        setTimeout(() => {
            isTriggeredFromWatchEffect.value = false;
        }, 50);

        return;
    }

    // All files are selected
    if (
        Object.values(allFiles.value.data).every(
            (file) => selectedFiles.value[file.id],
        )
    ) {
        isTriggeredFromWatchEffect.value = true;
        isAllFilesSelected.value = true;
        longPressedDirective.value = true;

        setTimeout(() => {
            isTriggeredFromWatchEffect.value = false;
        }, 50);

        return;
    }

    // some files are selected
    isTriggeredFromWatchEffect.value = true;
    isAllFilesSelected.value = false;

    setTimeout(() => {
        isTriggeredFromWatchEffect.value = false;
    }, 50);
});

const deleteFiles = async () => {
    try {
        const response = await axios.delete(route("file.destroy"), {
            data: {
                parent_id: props.folder.id,
                all: isAllFilesSelected.value,
                ids: isAllFilesSelected.value ? [] : selectedIds.value,
            },
        });

        if (response.data.status === "success") {
            toast({
                description: "Files deleted Successfully",
                variant: "success",
            });
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            toast({
                description: "Error occurred while deleting files",
                variant: "error",
            });
        }
    } catch (e) {
        toast({
            description: "Error occurred while deleting files",
            variant: "error",
        });
    }
};

const downloadFiles = async () => {
    try {
        const response = await axios.post(route("file.download"), {
            parent_id: props.folder.id,
            all: isAllFilesSelected.value,
            ids: isAllFilesSelected.value ? [] : selectedIds.value,
        });

        const a = document.createElement("a");
        a.download = response.data.filename;
        a.href = response.data.url;
        a.click();

        toast({
            description: "Files Downloaded Successfully",
            variant: "success",
        });
    } catch (e) {
        toast({
            description: e.response.data.message,
            variant: "error",
        });
    }
};

const truncate = (str, length) => {
    return str.length > length ? str.substring(0, length) + "..." : str;
};
</script>

<template>
    <Head title="My Files | BitBox" />

    <div
        class="mt-9 mb-5 flex flex-col gap-7 justify-between sm:flex-row sm:items-center"
    >
        <FilesBreadCrumb :ancestors="ancestors" />
        <div class="flex w-full sm:w-1/2 xl:w-1/3">
            <Button
                :disabled="selectedIds.length === 0"
                class="mr-4 basis-1/2"
                @click="downloadFiles"
            >
                <ArrowDownToLine class="me-2" />
                Download
            </Button>
            <AlertDialog>
                <AlertDialogTrigger
                    as="button"
                    :disabled="selectedIds.length == 0"
                    class="basis-1/2"
                >
                    <Button class="w-full" :disabled="selectedIds.length == 0">
                        <Trash class="me-2" />
                        Delete</Button
                    >
                </AlertDialogTrigger>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                        <AlertDialogDescription>
                            Are you sure want to delete these selected files?
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                        <AlertDialogAction @click="deleteFiles"
                            >Delete
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </div>
    </div>

    <div class="flex-1 overflow-x-scroll overflow-y-clip">
        <div v-if="allFiles.data.length > 0" class="flex flex-col">
            <div class="-m-1.5">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="overflow-hidden">
                        <div class="flex flex-col">
                            <div
                                class="-m-1.5 overflow-x-auto md:overflow-x-none"
                            >
                                <div
                                    class="p-1.5 min-w-full inline-block align-middle"
                                >
                                    <div class="overflow-hidden">
                                        <table
                                            class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700"
                                        >
                                            <thead>
                                                <tr class="text-center">
                                                    <th
                                                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500 text-center"
                                                        scope="col"
                                                        style="
                                                            text-align: center;
                                                        "
                                                        v-show="
                                                            !isMobile ||
                                                            longPressedDirective
                                                        "
                                                    >
                                                        <Checkbox
                                                            v-model:checked="
                                                                isAllFilesSelected
                                                            "
                                                        />
                                                    </th>

                                                    <th
                                                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500"
                                                        scope="col"
                                                    >
                                                        Name
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500"
                                                        scope="col"
                                                    >
                                                        Owner
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500"
                                                        scope="col"
                                                    >
                                                        Last Modified
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-500"
                                                        scope="col"
                                                    >
                                                        Size
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody
                                                class="divide-y divide-gray-200 dark:divide-neutral-700"
                                            >
                                                <tr
                                                    v-for="file in allFiles.data"
                                                    :key="`${file.id}-${selectedFiles[file.id]}`"
                                                    v-on-long-press.prevent="
                                                        (e) => {
                                                            onLongPressCallbackDirective(
                                                                file,
                                                                e,
                                                            );
                                                        }
                                                    "
                                                    :class="
                                                        selectedFiles[file.id]
                                                            ? 'bg-gray-100 dark:bg-neutral-700'
                                                            : ''
                                                    "
                                                    class="hover:bg-gray-100 dark:hover:bg-neutral-700 cursor-pointer"
                                                    @click="
                                                        isMobile &&
                                                        !longPressedDirective
                                                            ? openFolder(file)
                                                            : toggleCheckbox(
                                                                  file,
                                                                  $event,
                                                              )
                                                    "
                                                    @dblclick="
                                                        isMobile
                                                            ? null
                                                            : openFolder(file)
                                                    "
                                                >
                                                    <td
                                                        class="text-center"
                                                        v-show="
                                                            !isMobile ||
                                                            longPressedDirective
                                                        "
                                                    >
                                                        <Checkbox
                                                            v-model:checked="
                                                                selectedFiles[
                                                                    file.id
                                                                ]
                                                            "
                                                        />
                                                    </td>
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200 flex gap-2 items-center"
                                                    >
                                                        <component
                                                            :is="
                                                                fileIconMapper[
                                                                    getFileType(
                                                                        file.mime,
                                                                        file.is_folder,
                                                                    )
                                                                ]
                                                            "
                                                        />
                                                        <span>{{
                                                            truncate(
                                                                file.name,
                                                                40,
                                                            )
                                                        }}</span>
                                                    </td>
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200"
                                                    >
                                                        {{ file.owner }}
                                                    </td>
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200"
                                                    >
                                                        {{ file.updated_at }}
                                                    </td>
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium"
                                                    >
                                                        <span
                                                            v-if="
                                                                file.is_folder
                                                            "
                                                            class="flex justify-end"
                                                            ><Minus
                                                        /></span>
                                                        <span v-else>{{
                                                            file.size
                                                        }}</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="p-7 text-sm dark:text-gray-300 text-gray-600 my-9">
            No Folders Found, Drive is empty
        </div>

        <div ref="loadMoreRecordsContainerElement"></div>
    </div>
</template>

<style>
/* This will apply to all scrollbars on the page */
::-webkit-scrollbar {
    width: 6px; /* width of the entire scrollbar */
    height: 6px;
}

::-webkit-scrollbar-track {
    background: transparent; /* color of the tracking area */
}

::-webkit-scrollbar-thumb {
    background-color: #646363; /* color of the scroll thumb */
    //background-color: yellow; /* color of the scroll thumb */
    border-radius: 20px; /* roundness of the scroll thumb */
    border: 3px solid transparent; /* creates padding around scroll thumb */
}

/* For dark mode, you can use a media query to change the color of the scroll thumb */
@media (prefers-color-scheme: dark) {
    ::-webkit-scrollbar-thumb {
        background-color: #646363;
    }
}
</style>
