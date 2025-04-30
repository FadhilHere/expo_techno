<template>
    <SidebarProvider>
      <div class="w-full flex min-h-screen">
        <!-- Sidebar -->
        <Sidebar collapsible="icon" class="border-r border-border bg-background" variant="default">
          <SidebarHeader>
            <div class="flex items-center gap-3 px-4 py-3">
              <div class="flex h-9 w-9 items-center justify-center rounded-md bg-primary text-primary-foreground shadow-sm transition-all duration-200 hover:scale-105 hover:shadow-md">
                <span class="text-base font-bold">E</span>
              </div>
              <div class="flex flex-col transition-opacity duration-200 group-data-[collapsible=icon]:opacity-0">
                <span class="font-semibold text-sm">EXPO Tech</span>
                <span class="text-xs text-muted-foreground">Admin Dashboard</span>
              </div>
            </div>
            <SidebarSeparator class="my-1" />
          </SidebarHeader>

          <SidebarContent class="px-2 py-2">
            <SidebarMenu>
              <SidebarMenuItem to="/admin/dashboard" class="mb-1 group">
                <template #icon>
                  <LayoutDashboard class="h-4 w-4 mr-2" />
                </template>
                <span class="text-sm font-medium">Dashboard</span>
                <template #action>
                  <SidebarMenuBadge variant="secondary" class="text-xs">New</SidebarMenuBadge>
                </template>
              </SidebarMenuItem>

              <SidebarGroup class="mt-4">
                <template #label>
                  <span class="text-xs font-medium text-muted-foreground px-3 py-1">EXPO Management</span>
                </template>

                <SidebarMenuSub>
                  <template #icon>
                    <Calendar class="h-4 w-4 mr-2" />
                  </template>
                  <template #label>
                    <span class="text-sm font-medium">EXPO</span>
                  </template>
                  <template #action>
                    <ChevronDown class="h-3.5 w-3.5 opacity-50 transition-transform duration-200" />
                  </template>
                  <SidebarMenuSubItem to="/admin/expo/years" class="pl-9 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground rounded-md transition-all duration-200">
                    <template #icon>
                      <CalendarDays class="h-3.5 w-3.5 mr-2 opacity-70" />
                    </template>
                    Tahun EXPO
                  </SidebarMenuSubItem>
                  <SidebarMenuSubItem to="/admin/expo/info" class="pl-9 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground rounded-md transition-all duration-200">
                    <template #icon>
                      <Info class="h-3.5 w-3.5 mr-2 opacity-70" />
                    </template>
                    Informasi Website
                  </SidebarMenuSubItem>
                </SidebarMenuSub>
              </SidebarGroup>

              <SidebarSeparator class="my-3" />

              <SidebarGroup class="mt-1">
                <template #label>
                  <span class="text-xs font-medium text-muted-foreground px-3 py-1">Tenant Management</span>
                </template>

                <SidebarMenuSub>
                  <template #icon>
                    <Store class="h-4 w-4 mr-2" />
                  </template>
                  <template #label>
                    <span class="text-sm font-medium">Tenants</span>
                  </template>
                  <template #action>
                    <ChevronDown class="h-3.5 w-3.5 opacity-50 transition-transform duration-200" />
                  </template>
                  <SidebarMenuSubItem to="/admin/tenants/categories" class="pl-9 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground rounded-md transition-all duration-200">
                    <template #icon>
                      <FolderTree class="h-3.5 w-3.5 mr-2 opacity-70" />
                    </template>
                    Kategori Tenant
                    <template #action>
                      <Badge variant="outline" class="ml-auto text-xs">8</Badge>
                    </template>
                  </SidebarMenuSubItem>
                  <SidebarMenuSubItem to="/admin/tenants" class="pl-9 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground rounded-md transition-all duration-200">
                    <template #icon>
                      <Users class="h-3.5 w-3.5 mr-2 opacity-70" />
                    </template>
                    Daftar Tenant
                    <template #action>
                      <Badge variant="outline" class="ml-auto text-xs">14</Badge>
                    </template>
                  </SidebarMenuSubItem>
                  <SidebarMenuSubItem to="/admin/tenants/products" class="pl-9 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground rounded-md transition-all duration-200">
                    <template #icon>
                      <Package class="h-3.5 w-3.5 mr-2 opacity-70" />
                    </template>
                    Produk
                    <template #action>
                      <Badge variant="outline" class="ml-auto text-xs">87</Badge>
                    </template>
                  </SidebarMenuSubItem>
                </SidebarMenuSub>
              </SidebarGroup>

              <SidebarSeparator class="my-3" />

              <SidebarMenuItem to="/admin/orders" class="group">
                <template #icon>
                  <ShoppingCart class="h-4 w-4 mr-2" />
                </template>
                <span class="text-sm font-medium">Pre-Orders</span>
                <template #action>
                  <Badge variant="default" class="ml-auto text-xs">32</Badge>
                </template>
              </SidebarMenuItem>
            </SidebarMenu>
          </SidebarContent>

          <SidebarFooter>
            <SidebarSeparator />
            <div class="px-4 py-3">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="h-8 w-8 rounded-md bg-muted flex items-center justify-center transition-transform hover:scale-105">
                    <User2 class="h-4 w-4 text-muted-foreground" />
                  </div>
                  <div class="transition-opacity duration-200 group-data-[collapsible=icon]:opacity-0">
                    <p class="text-sm font-medium">Admin</p>
                    <p class="text-xs text-muted-foreground">admin@example.com</p>
                  </div>
                </div>
                <Button variant="ghost" size="icon" class="h-8 w-8 rounded-md hover:bg-destructive/10 hover:text-destructive transition-colors duration-200" @click="logout">
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
                  <Menu class="h-4 w-4" />
                </SidebarTrigger>
                <h1 class="text-lg font-semibold">{{ title }}</h1>
              </div>
              <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" class="relative h-8 w-8">
                  <Bell class="h-4 w-4" />
                  <span class="absolute -top-0.5 -right-0.5 h-3.5 w-3.5 rounded-full bg-primary text-[10px] font-medium text-primary-foreground flex items-center justify-center">
                    3
                  </span>
                </Button>
                <SidebarSeparator orientation="vertical" class="h-6" />
                <Button variant="outline" size="sm" class="h-8">
                  <Settings class="h-3.5 w-3.5 mr-1.5" />
                  <span class="text-xs">Settings</span>
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
  import { defineProps, ref } from 'vue';
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

  const props = defineProps({
    title: {
      type: String,
      default: 'Dashboard'
    }
  });

  // Logout function
  const logout = async () => {
    try {
      await axios.post('/logout');
      window.location.href = '/login';
    } catch (error) {
      console.error('Logout failed', error);
    }
  };
  </script>
