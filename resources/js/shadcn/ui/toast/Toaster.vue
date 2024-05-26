<script lang="ts" setup>
import { isVNode } from "vue";
import { useToast } from "./use-toast";
import { AlertTriangle, CircleCheck } from "lucide-vue-next";
import {
    Toast,
    ToastClose,
    ToastDescription,
    ToastProvider,
    ToastTitle,
    ToastViewport,
} from ".";

const { toasts } = useToast();
</script>

<template>
    <ToastProvider :duration="2500">
        <Toast v-for="toast in toasts" :key="toast.id" v-bind="toast">
            <div class="grid gap-1">
                <ToastTitle v-if="toast.title">
                    {{ toast.title }}
                </ToastTitle>
                <template v-if="toast.description">
                    <ToastDescription v-if="isVNode(toast.description)">
                        <component :is="toast.description" />
                    </ToastDescription>
                    <ToastDescription v-else>
                        <span class="flex items-center gap-2">
                            <CircleCheck
                                v-if="toast.variant === 'success'"
                                class="mr-2 h-14 w-14 text-green-500"
                            />
                            <AlertTriangle
                                v-else
                                class="mr-2 h-14 w-14 text-red-600"
                            />
                            {{ toast.description }}
                        </span>
                    </ToastDescription>
                </template>
                <ToastClose />
            </div>
            <component :is="toast.action" />
        </Toast>
        <ToastViewport />
    </ToastProvider>
</template>

<style scoped></style>
