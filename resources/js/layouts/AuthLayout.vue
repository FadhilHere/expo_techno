<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import AppSidebar from '@/components/AppSidebar.vue';
import {
    SidebarInset,
    SidebarProvider,
    SidebarTrigger
} from '@/components/ui/sidebar';
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { Separator } from '@/components/ui/separator';
import { Button } from '@/components/ui/button';
import { Moon, Sun } from 'lucide-vue-next';

// Interface for breadcrumb items
interface BreadcrumbItem {
    label: string;
    href: string | null;
}

// Props untuk title, description, dan breadcrumbs
const props = defineProps({
    title: {
        type: String,
        default: 'Dashboard'
    },
    description: {
        type: String,
        default: 'Welcome to your dashboard'
    },
    breadcrumbs: {
        type: Array as () => BreadcrumbItem[],
        default: null
    }
});

// Computed breadcrumbs (akan menggunakan default jika tidak ada yang diberikan)
const computedBreadcrumbs = computed<BreadcrumbItem[]>(() => {
    if (props.breadcrumbs) {
        return props.breadcrumbs;
    }

    // Default breadcrumbs berdasarkan title jika tidak ada yang diberikan
    return [
        { label: 'Menu', href: '/' },
        { label: props.title, href: null }
    ];
});

// Current year for footer
const currentYear = new Date().getFullYear();

// Theme state
const theme = ref<'light' | 'dark'>('light');

// Set initial theme based on local storage or system preference
onMounted(() => {
    // Check if theme is stored in localStorage
    const storedTheme = localStorage.getItem('theme');

    if (storedTheme) {
        theme.value = storedTheme as 'light' | 'dark';
        applyTheme(theme.value);
    } else {
        // Check system preference
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        theme.value = prefersDark ? 'dark' : 'light';
        applyTheme(theme.value);
    }
});

// Toggle theme function
const toggleTheme = () => {
    theme.value = theme.value === 'light' ? 'dark' : 'light';
    applyTheme(theme.value);
    localStorage.setItem('theme', theme.value);
};

// Apply theme to document
const applyTheme = (newTheme: string) => {
    if (newTheme === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};
</script>

<template>
    <SidebarProvider>
        <!-- AppSidebar di bagian luar, akan muncul di posisi fixed -->
        <AppSidebar />

        <!-- SidebarInset menampung konten utama dengan padding yang sesuai -->
        <SidebarInset class="flex flex-col min-h-screen">
            <!-- Header dengan breadcrumb dan tombol trigger sidebar -->
            <header class="flex h-16 shrink-0 items-center gap-2 border-b transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12">
                <div class="flex items-center justify-between w-full px-4">
                    <div class="flex items-center gap-2">
                        <SidebarTrigger class="-ml-1" />
                        <Separator orientation="vertical" class="mr-2 h-4" />
                        <!-- Dynamic Breadcrumb -->
                        <Breadcrumb>
                            <BreadcrumbList>
                                <template v-for="(item, index) in computedBreadcrumbs" :key="index">
                                    <!-- If it's not the last item and has href -->
                                    <BreadcrumbItem v-if="index < computedBreadcrumbs.length - 1 && item.href">
                                        <BreadcrumbLink :href="item.href">
                                            {{ item.label }}
                                        </BreadcrumbLink>
                                    </BreadcrumbItem>

                                    <!-- If it's the last item or doesn't have href -->
                                    <BreadcrumbItem v-else>
                                        <BreadcrumbPage>{{ item.label }}</BreadcrumbPage>
                                    </BreadcrumbItem>

                                    <!-- Add separator between items -->
                                    <BreadcrumbSeparator
                                        v-if="index < computedBreadcrumbs.length - 1"
                                    />
                                </template>
                            </BreadcrumbList>
                        </Breadcrumb>
                    </div>

                    <!-- Theme Toggle Button -->
                    <Button variant="outline" size="icon" @click="toggleTheme" aria-label="Toggle theme">
                        <Sun v-if="theme === 'dark'" class="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all" />
                        <Moon v-else class="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all" />
                    </Button>
                </div>
            </header>

            <!-- Main content area -->
            <div class="flex-1 flex flex-col p-6">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold">{{ title }}</h1>
                    <p class="text-sm text-muted-foreground">{{ description }}</p>
                </div>

                <!-- Slot untuk konten dari page yang menggunakan layout ini -->
                <div class="space-y-6 flex-1">
                    <slot></slot>
                </div>
            </div>

            <!-- Footer -->
            <footer class="py-4 px-6 border-t text-center text-sm text-muted-foreground">
                <p>© {{ currentYear }} Fadhil Parmata. All rights reserved.</p>
            </footer>
        </SidebarInset>
    </SidebarProvider>
</template>
