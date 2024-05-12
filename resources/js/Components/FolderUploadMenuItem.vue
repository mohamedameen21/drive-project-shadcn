<script lang="ts" setup>
import { Button } from "@/shadcn/ui/button";
import { CloudUpload } from "lucide-vue-next";
import Input from "../shadcn/ui/input/Input.vue";
import { emitter, FILE_UPLOAD_STARTED } from "@/event-bus";

const onChange = (event) => {
    console.log(event.target.files);
    emitter.emit(FILE_UPLOAD_STARTED, event.target.files);
};
</script>

<template>
    <button class="w-full flex items-center relative">
        <CloudUpload class="mr-2 h-4 w-4" />
        <span>Upload Folder</span>
        <input
            class="absolute left-0 right-0 top-0 bottom-0 cursor-pointer opacity-0 w-full h-full"
            directory
            type="file"
            webkitdirectory
            @change="onChange"
        />
    </button>
</template>

<style scoped></style>
