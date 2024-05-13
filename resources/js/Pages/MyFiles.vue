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

defineOptions({
    layout: AuthenticatedLayout,
});

const props = defineProps({
    files: Object,
    folder: Object,
    ancestors: Object,
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
</script>

<template>
    <div class="mt-9 mb-5">
        <FilesBreadCrumb :ancestors="ancestors" />
    </div>
    <div v-if="files.data.length > 0" class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class="overflow-hidden">
                    <div class="flex flex-col">
                        <div class="-m-1.5 overflow-x-auto">
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
                                                v-for="file in files.data"
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
                                                    <span>{{ file.name }}</span>
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
                                                        v-if="file.is_folder"
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
</template>

<style></style>
