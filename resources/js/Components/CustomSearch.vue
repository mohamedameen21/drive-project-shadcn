<script setup lang="ts">
import { Search } from "lucide-vue-next";
import Input from "../shadcn/ui/input/Input.vue";
import { onMounted, ref } from "vue";
import { router } from "@inertiajs/vue3";

const searchValue = ref("");

const getFiles = () => {
    console.log("va da");
    router.get(route("myFiles", { search: searchValue.value }));
};

onMounted(() => {
    const urlParam = new URLSearchParams(window.location.search);
    searchValue.value = urlParam.get("search");
});
</script>

<template>
    <div class="relative w-full items-center">
        <Input
            id="search"
            type="text"
            placeholder="Search for files and folder"
            class="pl-12 py-6 text-base focus:outline-none focus:border-0"
            v-model="searchValue"
            @input="getFiles"
        />
        <span
            class="absolute start-0 inset-y-0 flex items-center justify-center px-2"
        >
            <Search class="size-6 text-muted-foreground" />
        </span>
    </div>
</template>

<style scoped></style>
