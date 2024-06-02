<script setup lang="ts">
import { Search } from "lucide-vue-next";
import Input from "../shadcn/ui/input/Input.vue";
import { ref } from "vue";

const searchValue = ref("");
</script>

<template>
    <div class="relative w-full items-center">
        <Input
            id="search"
            type="text"
            placeholder="Search for files and folder"
            class="pl-12 py-6 text-base focus:outline-none focus:border-0"
            v-model="searchValue"
        />
        <span
            class="absolute start-0 inset-y-0 flex items-center justify-center px-2"
        >
            <Search class="size-6 text-muted-foreground" />
        </span>

        {{ searchValue }}
    </div>
</template>

<style scoped></style>
