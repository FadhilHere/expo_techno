<script setup lang="ts">
import { computed } from 'vue';

// Define interface for navigation items
interface NavItem {
    title: string;
    url: string;
    icon: any; // Icon component from lucide-vue-next
}

defineProps<{
    items: NavItem[];
}>();

// Get current path for active state highlighting
const currentPath = computed(() => window.location.pathname);
</script>

<template>
    <nav class="mt-2 px-2">
        <ul class="space-y-1">
            <li v-for="(item, index) in items" :key="index">
                <a
                    :href="item.url"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2 transition-colors hover:bg-gray-100 dark:hover:bg-gray-800',
                        currentPath === item.url ? 'bg-gray-100 dark:bg-gray-800 font-medium' : ''
                    ]"
                >
                    <component
                        :is="item.icon"
                        class="h-5 w-5"
                        :class="currentPath === item.url ? 'text-primary' : ''"
                    />
                    <span>{{ item.title }}</span>
                </a>
            </li>
        </ul>
    </nav>
</template>
