<script lang="ts" setup>
import { useColorMode } from "@vueuse/core";
import { Icon } from "@iconify/vue";
import { Button } from "@/shadcn/ui/button";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/shadcn/ui/dropdown-menu";

const mode = useColorMode();
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button
                class="fixed bottom-3.5 right-3.5 md:bottom-7 md:right-7 lg:bottom-14 lg:right-14 w-[60px] h-[60px] rounded-full"
                variant="outline"
            >
                <Icon
                    class="h-[1rem] w-[1rem] rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0"
                    icon="radix-icons:moon"
                />
                <Icon
                    class="absolute h-[1rem] w-[1rem] rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100"
                    icon="radix-icons:sun"
                />
                <span class="sr-only">Toggle theme</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuItem @click="mode = 'light'"> Light </DropdownMenuItem>
            <DropdownMenuItem @click="mode = 'dark'"> Dark </DropdownMenuItem>
            <DropdownMenuItem @click="mode = 'auto'"> System </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
