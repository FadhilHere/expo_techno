<template>
    <SidebarProvider>
        <div class="flex min-h-screen">
            <!-- Sidebar -->
            <Sidebar collapsible="icon" class="border-r" variant="default">
                <SidebarHeader>
                    <div class="flex items-center gap-3 px-4 py-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-sm transition-all duration-200 hover:scale-105 hover:shadow-md">
                            <span class="text-lg font-bold">E</span>
                        </div>
                        <div class="flex flex-col transition-opacity duration-200 group-data-[collapsible=icon]:opacity-0">
                            <span class="font-bold text-lg">EXPO Tech</span>
                            <span class="text-xs text-muted-foreground">Admin Dashboard</span>
                        </div>
                    </div>
                    <SidebarSeparator class="my-2" />
                </SidebarHeader>

                <SidebarContent class="px-2 py-2">
                    <SidebarMenu>
                        <SidebarMenuItem to="/admin/dashboard" class="mb-2 group">
                            <template #icon>
                                <LayoutDashboard class="h-5 w-5 transition-transform duration-200 group-hover:scale-110" />
                            </template>
                            <span class="font-medium">Dashboard</span>
                            <template #action>
                                <SidebarMenuBadge variant="default">New</SidebarMenuBadge>
                            </template>
                        </SidebarMenuItem>

                        <SidebarGroup>
                            <template #label>
                                <span class="text-xs font-medium text-muted-foreground px-4">EXPO Management</span>
                            </template>

                            <SidebarMenuSub>
                                <template #icon>
                                    <Calendar class="h-5 w-5 transition-colors duration-200" />
                                </template>
                                <template #label>
                                    <span class="font-medium">EXPO</span>
                                </template>
                                <template #action>
                                    <ChevronDown class="h-4 w-4 transition-transform duration-200" />
                                </template>
                                <SidebarMenuSubItem to="/admin/expo/years" class="pl-10 py-2 hover:bg-muted/50 rounded-lg transition-all duration-200">
                                    <template #icon>
                                        <CalendarDays class="h-4 w-4 mr-2" />
                                    </template>
                                    Tahun EXPO
                                </SidebarMenuSubItem>
                                <SidebarMenuSubItem to="/admin/expo/info" class="pl-10 py-2 hover:bg-muted/50 rounded-lg transition-all duration-200">
                                    <template #icon>
                                        <Info class="h-4 w-4 mr-2" />
                                    </template>
                                    Informasi Website
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </SidebarGroup>

                        <SidebarSeparator class="my-4" />

                        <SidebarGroup>
                            <template #label>
                                <span class="text-xs font-medium text-muted-foreground px-4">Tenant Management</span>
                            </template>

                            <SidebarMenuSub>
                                <template #icon>
                                    <Store class="h-5 w-5 transition-colors duration-200" />
                                </template>
                                <template #label>
                                    <span class="font-medium">Tenants</span>
                                </template>
                                <template #action>
                                    <ChevronDown class="h-4 w-4 transition-transform duration-200" />
                                </template>
                                <SidebarMenuSubItem to="/admin/tenants/categories" class="pl-10 py-2 hover:bg-muted/50 rounded-lg transition-all duration-200">
                                    <template #icon>
                                        <FolderTree class="h-4 w-4 mr-2" />
                                    </template>
                                    Kategori Tenant
                                    <template #action>
                                        <Badge variant="outline" class="ml-auto">8</Badge>
                                    </template>
                                </SidebarMenuSubItem>
                                <SidebarMenuSubItem to="/admin/tenants" class="pl-10 py-2 hover:bg-muted/50 rounded-lg transition-all duration-200">
                                    <template #icon>
                                        <Users class="h-4 w-4 mr-2" />
                                    </template>
                                    Daftar Tenant
                                    <template #action>
                                        <Badge variant="outline" class="ml-auto">14</Badge>
                                    </template>
                                </SidebarMenuSubItem>
                                <SidebarMenuSubItem to="/admin/tenants/products" class="pl-10 py-2 hover:bg-muted/50 rounded-lg transition-all duration-200">
                                    <template #icon>
                                        <Package class="h-4 w-4 mr-2" />
                                    </template>
                                    Produk
                                    <template #action>
                                        <Badge variant="outline" class="ml-auto">87</Badge>
                                    </template>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </SidebarGroup>

                        <SidebarSeparator class="my-4" />

                        <SidebarMenuItem to="/admin/orders" class="group">
                            <template #icon>
                                <ShoppingCart class="h-5 w-5 transition-transform duration-200 group-hover:scale-110" />
                            </template>
                            <span class="font-medium">Pre-Orders</span>
                            <template #action>
                                <Badge variant="default" class="bg-primary">32</Badge>
                            </template>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarContent>

                <SidebarFooter>
                    <SidebarSeparator />
                    <div class="px-4 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-muted flex items-center justify-center transition-transform hover:scale-105">
                                    <User2 class="h-5 w-5 text-muted-foreground" />
                                </div>
                                <div class="transition-opacity duration-200 group-data-[collapsible=icon]:opacity-0">
                                    <p class="text-sm font-medium">Admin</p>
                                    <p class="text-xs text-muted-foreground">admin@example.com</p>
                                </div>
                            </div>
                            <Button variant="ghost" size="icon" class="hover:bg-destructive/10 hover:text-destructive transition-colors duration-200" @click="logout">
                                <LogOut class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </SidebarFooter>
            </Sidebar>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col">
                <!-- Header -->
                <header class="sticky top-0 z-10 border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
                    <div class="flex h-14 items-center justify-between px-4">
                        <div class="flex items-center gap-2">
                            <SidebarTrigger class="lg:hidden">
                                <Menu class="h-5 w-5" />
                            </SidebarTrigger>
                            <h1 class="text-xl font-semibold">{{ title }}</h1>
                        </div>
                        <div class="flex items-center gap-4">
                            <Button variant="ghost" size="icon" class="relative">
                                <Bell class="h-5 w-5" />
                                <span class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-primary text-[10px] font-medium text-primary-foreground flex items-center justify-center">
                                    3
                                </span>
                            </Button>
                            <SidebarSeparator orientation="vertical" class="h-6" />
                            <Button variant="outline" size="sm">
                                <Settings class="h-4 w-4 mr-2" />
                                Settings
                            </Button>
                        </div>
                    </div>
                </header>

                <!-- Main Content Area -->
                <main class="flex-1 overflow-auto p-4">
                    <slot></slot>
                </main>
            </div>
        </div>
    </SidebarProvider>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';
import axios from 'axios';
import {
    Bell,
    Menu,
    LogOut,
    LayoutDashboard,
    Calendar,
    CalendarDays,
    Store,
    ShoppingCart,
    User2,
    ChevronDown,
    Info,
    FolderTree,
    Users,
    Package,
    Settings
} from 'lucide-vue-next';

import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    Sidebar,
    SidebarHeader,
    SidebarContent,
    SidebarFooter,
    SidebarMenu,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubItem,
    SidebarTrigger,
    SidebarProvider,
    SidebarGroup,
    SidebarSeparator,
    SidebarMenuBadge
} from '@/components/ui/sidebar';

defineProps({
    title: {
        type: String,
        default: 'Dashboard'
    }
});

// Logout function
const logout = async () => {
    try {
        await axios.post('/logout');
        window.location.href = '/auth/Login.vue';
    } catch (error) {
        console.error('Logout failed', error);
    }
};
</script>
