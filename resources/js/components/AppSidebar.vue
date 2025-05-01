<script setup lang="ts">
import type { SidebarProps } from '@/components/ui/sidebar'

import NavMain from '@/components/NavMain.vue'
import NavProjects from '@/components/NavProjects.vue'
import NavSimple from '@/components/NavSimple.vue'
import NavUser from '@/components/NavUser.vue'
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarRail,
  useSidebar,
  SidebarGroupLabel,
} from '@/components/ui/sidebar'

import {
  AudioWaveform,
  BookOpen,
  Bot,
  Command,
  Frame,
  GalleryVerticalEnd,
  Map,
  PieChart,
  Settings2,
  SquareTerminal,
  TimerIcon,
  Square,
  Store,
} from 'lucide-vue-next'

const props = withDefaults(defineProps<SidebarProps>(), {
  collapsible: 'icon',
})

const { state } = useSidebar()

// This is sample data.
const data = {
  user: {
    name: 'Techno',
    email: 'Techno Sistem Informasi',
    avatar: '/avatars/shadcn.jpg',
  },
  simpleNav: [
    {
      title: 'Dashboard',
      url: '/admin/dashboard',
      icon: PieChart,
    },
    {
      title: 'Tahun Expo',
      url: '/admin/tahun-expo',
      icon: TimerIcon
    },
    {
      title: 'Kategori Tenant',
      url: '/admin/kategori-tenant',
      icon: Square
    },
    {
      title: 'Tenant',
      url: '/admin/tenant',
      icon: Store
    },
  ],
  navMain: [
    {
      title: 'Playground',
      url: '#',
      icon: SquareTerminal,
      items: [
        {
          title: 'History',
          url: '#',
        },
        {
          title: 'Starred',
          url: '#',
        },
        {
          title: 'Settings',
          url: '#',
        },
      ],
    },
    {
      title: 'Models',
      url: '#',
      icon: Bot,
      items: [
        {
          title: 'Genesis',
          url: '#',
        },
        {
          title: 'Explorer',
          url: '#',
        },
        {
          title: 'Quantum',
          url: '#',
        },
      ],
    },
    {
      title: 'Documentation',
      url: '#',
      icon: BookOpen,
      items: [
        {
          title: 'Introduction',
          url: '#',
        },
        {
          title: 'Get Started',
          url: '#',
        },
        {
          title: 'Tutorials',
          url: '#',
        },
        {
          title: 'Changelog',
          url: '#',
        },
      ],
    },
    {
      title: 'Settings',
      url: '#',
      icon: Settings2,
      items: [
        {
          title: 'General',
          url: '#',
        },
        {
          title: 'Team',
          url: '#',
        },
        {
          title: 'Billing',
          url: '#',
        },
        {
          title: 'Limits',
          url: '#',
        },
      ],
    },
  ],
  projects: [
    {
      name: 'Design Engineering',
      url: '#',
      icon: Frame,
    },
    {
      name: 'Sales & Marketing',
      url: '#',
      icon: PieChart,
    },
    {
      name: 'Travel',
      url: '#',
      icon: Map,
    },
  ],
}
</script>

<template>
  <Sidebar v-bind="props">
    <SidebarHeader>
      <div class="flex items-center justify-center p-4">
        <div class="flex aspect-square size-8 items-center justify-center rounded-lg bg-sidebar-primary text-sidebar-primary-foreground">
          <GalleryVerticalEnd class="size-4" v-if="state === 'expanded'" />
          <SquareTerminal class="size-4" v-else />
        </div>
      </div>
    </SidebarHeader>
    <SidebarContent>
        <SidebarGroupLabel>Data Master</SidebarGroupLabel>
      <NavSimple :items="data.simpleNav" />
      <NavMain :items="data.navMain" />
      <NavProjects :projects="data.projects" />
    </SidebarContent>
    <SidebarFooter>
      <NavUser :user="data.user" />
    </SidebarFooter>
    <SidebarRail />
  </Sidebar>
</template>
