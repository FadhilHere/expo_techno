<script setup lang="ts">
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

// Props untuk title dan description yang bisa dioverride oleh komponen yang menggunakan layout ini
defineProps({
    title: {
        type: String,
        default: 'Dashboard'
    },
    description: {
        type: String,
        default: 'Welcome to your dashboard'
    }
});
</script>

<template>
    <SidebarProvider>
        <!-- AppSidebar di bagian luar, akan muncul di posisi fixed -->
        <AppSidebar />

        <!-- SidebarInset menampung konten utama dengan padding yang sesuai -->
        <SidebarInset>
            <!-- Header dengan breadcrumb dan tombol trigger sidebar -->
            <header class="flex h-16 shrink-0 items-center gap-2 border-b transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12">
                <div class="flex items-center gap-2 px-4">
                    <SidebarTrigger class="-ml-1" />
                    <Separator orientation="vertical" class="mr-2 h-4" />
                    <Breadcrumb>
                        <BreadcrumbList>
                            <BreadcrumbItem class="hidden md:block">
                                <BreadcrumbLink href="/">
                                    Dashboard
                                </BreadcrumbLink>
                            </BreadcrumbItem>
                            <BreadcrumbSeparator class="hidden md:block" />
                            <BreadcrumbItem>
                                <BreadcrumbPage>{{ title }}</BreadcrumbPage>
                            </BreadcrumbItem>
                        </BreadcrumbList>
                    </Breadcrumb>
                </div>
            </header>

            <!-- Main content area -->
            <div class="flex flex-1 flex-col p-6">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold">{{ title }}</h1>
                    <p class="text-sm text-muted-foreground">{{ description }}</p>
                </div>

                <!-- Slot untuk konten dari page yang menggunakan layout ini -->
                <div class="space-y-6">
                    <slot></slot>
                </div>
            </div>
        </SidebarInset>
    </SidebarProvider>
</template>
