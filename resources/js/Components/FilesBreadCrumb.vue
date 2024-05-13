<script lang="ts" setup>
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbSeparator,
} from "@/shadcn/ui/breadcrumb";
import { Home } from "lucide-vue-next";
import { Link } from "@inertiajs/vue3";

defineProps({
    ancestors: Object,
});
</script>

<template>
    <Breadcrumb>
        <BreadcrumbList>
            <BreadcrumbItem
                v-for="(folder, index) in ancestors.data"
                class="text-base"
            >
                <BreadcrumbLink v-if="!folder.parent_id">
                    <Link
                        :href="route('myFiles')"
                        class="flex items-center justify-center gap-2"
                    >
                        <Home class="inline-block text-center" size="16" />
                        <span>My Files</span>
                    </Link>
                </BreadcrumbLink>
                <BreadcrumbLink v-else>
                    <Link :href="route('myFiles', { folderPath: folder.path })">
                        <span>{{ folder.name }}</span>
                    </Link>
                </BreadcrumbLink>
                <BreadcrumbSeparator
                    v-if="index < ancestors.data?.length - 1"
                />
            </BreadcrumbItem>
        </BreadcrumbList>
    </Breadcrumb>
</template>
