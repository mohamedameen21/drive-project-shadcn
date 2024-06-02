<script lang="ts" setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
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
} from "lucide-vue-next";
import { onMounted, ref } from "vue";
import axios from "axios";

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

const openFolder = (file) => {
    if (!file.is_folder) return;

    router.visit(route("myFiles", { folderPath: file.path }));
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
};

const lodeMoreContent = async () => {
    if (allFiles.value.next) {
        // convert this in async await
        const response = await axios.get(allFiles.value.next);
        allFiles.value.data = [...allFiles.value.data, ...response.data.data];
        allFiles.value.next = response.data.links.next;
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
</script>

<template>
    <div class="mt-9 mb-5">
        <FilesBreadCrumb :ancestors="ancestors" />
    </div>

    <div class="flex-1 overflow-y-auto">
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
                                                <tr>
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
                                                    class="hover:bg-gray-100 dark:hover:bg-neutral-700 cursor-pointer"
                                                    @click="openFolder(file)"
                                                >
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200 flex gap-2 items-center"
                                                    >
                                                        <component
                                                            :is="
                                                                fileIconMapper[
                                                                    getFileType(
                                                                        file.mime,
                                                                    )
                                                                ]
                                                            "
                                                        />
                                                        <span>{{
                                                            file.name
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
