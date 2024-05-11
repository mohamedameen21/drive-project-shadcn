<script lang="ts" setup>
import { onMounted, ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import Dark from "@/Pages/Dark.vue";
import { emitter, FILE_UPLOAD_STARTED } from "@/event-bus";
import CustomeSearch from "@/Components/CustomeSearch.vue";
import CreateNewDropdown from "@/Components/CreateNewDropdown.vue";

const page = usePage();
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
    console.log("uploading files");
    fileUploadForm.parent_id = page.props.folder.id;
    fileUploadForm.files = files;

    //The webkitRelativePath will have the subdirectory structure of the files
    // As the File object will only give the idea only on the file name & type etc not path.
    // Then we are slicing the first 20 files path.
    // the Files are automatically skipped by the php as the default config of the file upload limit is 20 files per request
    fileUploadForm.relative_paths = [...files]
        .map((file) => file.webkitRelativePath)
        .slice(0, 20);

    fileUploadForm.post(route("file.store"));
    console.log("bro");
};

const onDragOver = (event) => {
    console.log(" drag over");
    dragOver.value = true;
};

const onDragLeave = (event) => {
    console.log(" drag leave");
    dragOver.value = false;
};

const handleDrop = (event) => {
    console.log("dropped");
    dragOver.value = false;
    console.log(event);
    console.log(event.dataTransfer.files);
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
            <nav
                class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700"
            >
                <!-- Primary Navigation Menu -->
                <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :active="route().current('dashboard')"
                                    :href="route('dashboard')"
                                >
                                    Dashboard
                                </NavLink>

                                <NavLink
                                    :active="route().current('myFiles')"
                                    :href="route('myFiles')"
                                >
                                    My Files
                                </NavLink>

                                <NavLink
                                    :active="route().current('shared-with-me')"
                                    :href="route('shared-with-me')"
                                >
                                    Shared with me
                                </NavLink>

                                <NavLink
                                    :active="route().current('shared-my-me')"
                                    :href="route('shared-by-me')"
                                >
                                    Shared by me
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                                                type="button"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                >
                                                    <path
                                                        clip-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        fill-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            as="button"
                                            method="post"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>

                            <div>
                                <span class="ms-4">
                                    <Dark />
                                </span>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                            >
                                <svg
                                    class="h-6 w-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        d="M4 6h16M4 12h16M4 18h16"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        d="M6 18L18 6M6 6l12 12"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink
                            :active="route().current('dashboard')"
                            :href="route('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            :active="route().current('myFiles')"
                            :href="route('myFiles')"
                        >
                            My Files
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600"
                    >
                        <div class="px-4">
                            <div
                                class="font-medium text-base text-gray-800 dark:text-gray-200"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                as="button"
                                method="post"
                            >
                                Log Out
                            </ResponsiveNavLink>
                            <ResponsiveNavLink as="button" href="#">
                                <div>
                                    Theme
                                    <span class="ms-4">
                                        <Dark />
                                    </span>
                                </div>
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

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
                                        <CustomeSearch />
                                    </main>

                                    <slot />
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </main>
        </div>
    </div>
</template>
