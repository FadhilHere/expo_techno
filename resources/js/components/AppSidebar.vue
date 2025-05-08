<script setup lang="ts">
import type { SidebarProps } from '@/components/ui/sidebar';

// import NavMain from '@/components/NavMain.vue'
// import NavProjects from '@/components/NavProjects.vue'
import NavSimple from '@/components/NavSimple.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarGroupLabel, SidebarHeader, SidebarRail } from '@/components/ui/sidebar';

import { usePage } from '@inertiajs/vue3';
import { PieChart, ShoppingCart, Square, Store, TimerIcon, User } from 'lucide-vue-next';
import { computed, onMounted } from 'vue';

const props = withDefaults(defineProps<SidebarProps>(), {
    collapsible: 'icon',
});

// Get current user from Inertia shared props
const currentUser = computed(() => {
    // Uncomment this line for testing if needed
    // alert('Trying to access Inertia props');

    try {
        const pageProps = usePage().props;
        // console.log('All Inertia shared props:', pageProps);

        // Direct access for debugging
        // if (pageProps && pageProps.auth && pageProps.auth.user) {
        // console.log('DIRECT USER ACCESS SUCCESSFUL');
        // }

        const user = pageProps.auth?.user;
        // console.log('Auth user data from Inertia:', user);

        if (!user) {
            // console.warn('No user data found in auth props. Make sure middleware is properly sharing auth data.');
        }

        return user;
    } catch (error) {
        console.error('ERROR ACCESSING INERTIA PROPS:', error);
        return null;
    }
});

// For debugging - log when component mounts
onMounted(() => {
    // Add a simple alert that will definitely show up
    // Comment this out after debugging
    // alert('AppSidebar component mounted!');

    // console.log('AppSidebar mounted, currentUser:', currentUser.value);

    // Check if the user object has the expected properties
    if (currentUser.value) {
        // console.log('User properties available:', Object.keys(currentUser.value));
        // console.log('Username:', currentUser.value.username);
        // console.log('Role:', currentUser.value.role);
    } else {
        // console.warn('User is not authenticated or auth data is not being passed correctly');
    }
});

// This is data for user sidebar.
const data = {
    allNavItems: [
        {
            title: 'Dashboard',
            url: '/admin/dashboard',
            icon: PieChart,
            roles: ['admin', 'super_admin'],
        },
        {
            title: 'Tahun Expo',
            url: '/admin/tahun-expo',
            icon: TimerIcon,
            roles: ['admin', 'super_admin'],
        },
        {
            title: 'Kategori Tenant',
            url: '/admin/kategori-tenant',
            icon: Square,
            roles: ['admin', 'super_admin'],
        },
        {
            title: 'Tenant',
            url: '/admin/tenant',
            icon: Store,
            roles: ['admin', 'super_admin'],
        },
        {
            title: 'Pre-Order',
            url: '/admin/pre-orders',
            icon: ShoppingCart,
            roles: ['admin', 'super_admin'],
        },
        {
            title: 'Account',
            url: '/super-admin/account',
            icon: User,
            roles: ['super_admin'],
        },
        {
            title: 'Dashboard Mahasiswa',
            url: '/mahasiswa/dashboard',
            icon: User,
            roles: ['mahasiswa'],
        },
        {
            title: 'Tenant Mahasiswa',
            url: '/mahasiswa/tenant',
            icon: Store,
            roles: ['mahasiswa'],
        },
        {
            title: 'PreOrder Mahasiswa',
            url: '/mahasiswa/pre-orders',
            icon: ShoppingCart,
            roles: ['mahasiswa'],
        },
        {
            title: 'Pemesanan Onsite',
            url: '/mahasiswa/on-site-orders',
            icon: ShoppingCart,
            roles: ['mahasiswa'],
        },
    ],
    //   navMain: [
    //     {
    //       title: 'Playground',
    //       url: '#',
    //       icon: SquareTerminal,
    //       items: [
    //         {
    //           title: 'History',
    //           url: '#',
    //         },
    //         {
    //           title: 'Starred',
    //           url: '#',
    //         },
    //         {
    //           title: 'Settings',
    //           url: '#',
    //         },
    //       ],
    //     },
    //     {
    //       title: 'Models',
    //       url: '#',
    //       icon: Bot,
    //       items: [
    //         {
    //           title: 'Genesis',
    //           url: '#',
    //         },
    //         {
    //           title: 'Explorer',
    //           url: '#',
    //         },
    //         {
    //           title: 'Quantum',
    //           url: '#',
    //         },
    //       ],
    //     },
    //     {
    //       title: 'Documentation',
    //       url: '#',
    //       icon: BookOpen,
    //       items: [
    //         {
    //           title: 'Introduction',
    //           url: '#',
    //         },
    //         {
    //           title: 'Get Started',
    //           url: '#',
    //         },
    //         {
    //           title: 'Tutorials',
    //           url: '#',
    //         },
    //         {
    //           title: 'Changelog',
    //           url: '#',
    //         },
    //       ],
    //     },
    //     {
    //       title: 'Settings',
    //       url: '#',
    //       icon: Settings2,
    //       items: [
    //         {
    //           title: 'General',
    //           url: '#',
    //         },
    //         {
    //           title: 'Team',
    //           url: '#',
    //         },
    //         {
    //           title: 'Billing',
    //           url: '#',
    //         },
    //         {
    //           title: 'Limits',
    //           url: '#',
    //         },
    //       ],
    //     },
    //   ],
    //   projects: [
    //     {
    //       name: 'Design Engineering',
    //       url: '#',
    //       icon: Frame,
    //     },
    //     {
    //       name: 'Sales & Marketing',
    //       url: '#',
    //       icon: PieChart,
    //     },
    //     {
    //       name: 'Travel',
    //       url: '#',
    //       icon: Map,
    //     },
    //   ],
};

// Function to format role text for display
const formatRoleForDisplay = (role: string): string => {
    if (!role) return 'No Role';

    // Handle specific role formats
    switch (role.toLowerCase()) {
        case 'super_admin':
            return 'Super Admin';
        case 'admin':
            return 'Admin';
        case 'mahasiswa':
            return 'Mahasiswa';
        default:
            // For any other roles, capitalize first letter of each word and replace underscores with spaces
            return role
                .split('_')
                .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                .join(' ');
    }
};

// Make user data a separate reactive property, not nested inside data object
const userData = computed(() => {
    // console.log('Computing user data for sidebar, currentUser:', currentUser.value);

    // For a more reliable fallback, use static data if currentUser is not available
    if (!currentUser.value) {
        console.warn('Using fallback user data for sidebar');
        return {
            name: 'Guest User',
            email: 'No Role',
            avatar: '/assets/Logo_polos.png',
        };
    }

    // Extract user fields with better error handling
    const username = currentUser.value.username || currentUser.value.name || 'Unknown User';
    const role = currentUser.value.role || 'No Role';

    // Format the role for display
    const formattedRole = formatRoleForDisplay(role);

    // console.log('Using actual user data:', { username, role, formattedRole });

    return {
        name: username,
        email: formattedRole, // Use the formatted role
        avatar: '/assets/Logo_polos.png',
    };
});

// Create a computed property that filters navigation items based on user role
const simpleNav = computed(() => {
    return data.allNavItems.filter((item) => {
        // If the item has no roles restriction, show it to all users
        if (!item.roles) return true;

        // If the item has roles restriction, check if the current user has the required role
        return item.roles.includes(currentUser.value?.role);
    });
});
</script>

<template>
    <Sidebar v-bind="props">
        <SidebarHeader>
            <div class="flex items-center justify-center p-4">
                <div class="bg-sidebar-primary text-sidebar-primary-foreground flex aspect-square size-8 items-center justify-center rounded-lg">
                    <img :src="'/assets/Logo_polos.png'" alt="Logo" class="size-4" />
                </div>
            </div>
        </SidebarHeader>
        <SidebarContent>
            <!-- Debug info - REMOVE AFTER FIXING -->
            <!-- <div style="background-color: red; color: white; padding: 8px; margin: 4px; font-size: 12px;">
                <div>DEBUG OUTPUT:</div>
                <div>Has currentUser: {{ currentUser ? 'YES' : 'NO' }}</div>
                <div v-if="currentUser">Username: {{ currentUser.username }}</div>
                <div v-if="currentUser">Role: {{ currentUser.role }}</div>
            </div> -->

            <SidebarGroupLabel>Data Master</SidebarGroupLabel>
            <NavSimple :items="simpleNav" />
            <!-- <NavMain :items="data.navMain" /> -->
            <!-- <NavProjects :projects="data.projects" /> -->
        </SidebarContent>
        <SidebarFooter>
            <NavUser :user="userData" />
        </SidebarFooter>
        <SidebarRail />
    </Sidebar>
</template>
