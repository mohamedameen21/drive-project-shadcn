<script lang="ts" setup>
const props = defineProps({
    form: Object,
});
</script>

<template>
    <div
        v-if="form.progress"
        class="absolute border-gray-900 right-8 bottom-4 bg-white shadow-lg rounded flex flex-col w-[240px] p-3 dark:text-white dark:bg-[#27272a]"
    >
        <div class="text-xs mb-2">Uploading {{ form.files.length }} files</div>
        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
            <div
                :style="{ width: `${form.progress.percentage}%` }"
                class="h-full bg-gray-900 rounded-md transition-all"
            ></div>
        </div>
    </div>
</template>

<style scoped></style>
