<script setup lang="ts">
import { Home, Info, ShoppingCart, Store, TimerIcon, PieChart, Square } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import { useRouter } from 'vue-router';
import { computed } from 'vue';

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
});

const router = useRouter();

// Get the current route to highlight active link
const currentRoute = computed(() => router.currentRoute.value.path);
</script>

<template>
    <nav class="mt-2 px-2">
        <ul class="space-y-1">
            <li v-for="(item, index) in items" :key="index">
                <a
                    :href="item.url"
                    :class="[
                        'text-sidebar-foreground hover:bg-sidebar-hover group flex items-center gap-3 rounded-lg px-3 py-2 transition-colors',
                        currentRoute === item.url ? 'bg-sidebar-hover font-medium' : ''
                    ]"
                >
                    <component
                        :is="item.icon"
                        class="size-5"
                        :class="currentRoute === item.url ? 'text-primary' : ''"
                    />
                    <span>{{ item.title }}</span>
                </a>
            </li>
        </ul>
    </nav>
</template>