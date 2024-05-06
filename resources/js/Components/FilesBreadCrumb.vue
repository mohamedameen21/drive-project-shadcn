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
    ancestors: Array,
});
</script>

<template>
    <Breadcrumb>
        <BreadcrumbList>
            <BreadcrumbItem
                class="text-base"
                v-for="(folder, index) in ancestors.data"
            >
                <BreadcrumbLink v-if="!folder.parent_id">
                    <Link
                        :href="route('myFiles')"
                        class="flex items-center justify-center gap-2"
                    >
                        <Home size="16" class="inline-block text-center" />
                        <span>My Files</span>
                    </Link>
                </BreadcrumbLink>

                <BreadcrumbLink v-else>
                    <Link :href="route('myFiles', { folder: folder.name })">
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
