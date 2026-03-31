<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import {
    LayoutDashboard,
    Users,
    Building2,
    Package,
    LogOut,
    User,
    ChevronDown,
    Menu,
    PanelLeftClose,
    PanelLeft,
} from 'lucide-vue-next';
import { computed, ref, onMounted } from 'vue';

const SIDEBAR_COOKIE = 'admin_sidebar_collapsed';

const page = usePage();
const user = page.props.auth?.user;

const collapsed = ref(false);

onMounted(() => {
    const stored = localStorage.getItem(SIDEBAR_COOKIE);
    collapsed.value = stored === 'true';
});

function toggleSidebar() {
    collapsed.value = !collapsed.value;
    localStorage.setItem(SIDEBAR_COOKIE, String(collapsed.value));
}

const navItems = [
    { title: 'Dashboard', href: '/admin', icon: LayoutDashboard },
    { title: 'Tenants', href: '/admin/tenants', icon: Building2 },
    { title: 'Customers', href: '/admin/customers', icon: Users },
    { title: 'Contact Requests', href: '/admin/product-requests', icon: Package },
];

const pageTitle = computed(() => {
    const url = page.url;
    if (url === '/admin' || url.startsWith('/admin?')) return 'Dashboard';
    if (url.startsWith('/admin/tenants')) return 'Tenants';
    if (url.startsWith('/admin/customers')) return 'Customers';
    if (url.startsWith('/admin/product-requests')) return 'Contact Requests';
    if (url.startsWith('/admin/profile')) return 'Profile';
    return 'Admin';
});

const isActive = (href: string) => {
    if (href === '/admin') return page.url === '/admin' || page.url === '/admin/';
    return page.url.startsWith(href);
};
</script>

<template>
    <TooltipProvider :delay-duration="0">
        <div class="flex min-h-screen bg-zinc-100">
            <!-- Sidebar - Desktop: fixed left, collapsible -->
            <aside
                :class="[
                    'fixed inset-y-0 left-0 z-30 hidden flex-col border-r border-zinc-800 bg-zinc-900 transition-[width] duration-200 md:flex',
                    collapsed ? 'w-16' : 'w-64',
                ]"
            >
                <!-- Logo + Toggle -->
                <div
                    :class="[
                        'flex h-16 shrink-0 items-center border-b border-zinc-800 transition-colors',
                        collapsed ? 'justify-center px-0' : 'gap-3 px-4',
                    ]"
                >
                    <Tooltip :delay-duration="0" :disabled="!collapsed">
                        <TooltipTrigger as-child>
                            <Link
                                href="/admin"
                                class="flex shrink-0 items-center gap-3"
                                :class="collapsed ? 'justify-center' : ''"
                            >
                                <div
                                    class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white"
                                >
                                    <LayoutDashboard class="size-5" />
                                </div>
                                <div
                                    v-show="!collapsed"
                                    class="flex flex-col overflow-hidden"
                                >
                                    <span class="font-semibold text-white">ReadyMind</span>
                                    <span class="text-xs text-zinc-400">Admin Portal</span>
                                </div>
                            </Link>
                        </TooltipTrigger>
                        <TooltipContent side="right" hide-arrow class="border-zinc-700 bg-zinc-800 text-white">
                            ReadyMind Admin
                        </TooltipContent>
                    </Tooltip>
                    <Button
                        v-show="!collapsed"
                        variant="ghost"
                        size="icon"
                        class="ml-auto size-8 text-zinc-400 hover:bg-zinc-800 hover:text-white"
                        @click="toggleSidebar"
                    >
                        <PanelLeftClose class="size-4" />
                    </Button>
                </div>

                <!-- Toggle when collapsed -->
                <div v-show="collapsed" class="flex justify-center border-b border-zinc-800 py-2">
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <Button
                                variant="ghost"
                                size="icon"
                                class="size-8 text-zinc-400 hover:bg-zinc-800 hover:text-white"
                                @click="toggleSidebar"
                            >
                                <PanelLeft class="size-4" />
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent side="right" hide-arrow class="border-zinc-700 bg-zinc-800 text-white">
                            Expandir
                        </TooltipContent>
                    </Tooltip>
                </div>

                <!-- Nav -->
                <div
                    :class="[
                        'flex-1 overflow-y-auto py-6',
                        collapsed ? 'px-2' : 'px-4',
                    ]"
                >
                    <p
                        v-show="!collapsed"
                        class="mb-3 px-3 text-xs font-medium uppercase tracking-wider text-zinc-500"
                    >
                        Navigation
                    </p>
                    <nav class="flex flex-col gap-1">
                        <Tooltip v-for="item in navItems" :key="item.href" :delay-duration="0" :disabled="!collapsed">
                            <TooltipTrigger as-child>
                                <span class="inline-block w-full">
                                    <Link
                                        :href="item.href"
                                        :class="[
                                            'flex w-full items-center rounded-lg py-2.5 text-sm font-medium transition-colors',
                                            collapsed ? 'justify-center px-0' : 'gap-3 px-3',
                                            isActive(item.href)
                                                ? 'bg-indigo-600 text-white'
                                                : 'text-zinc-300 hover:bg-zinc-800 hover:text-white',
                                        ]"
                                    >
                                        <component :is="item.icon" class="size-5 shrink-0" />
                                        <span v-show="!collapsed" class="truncate">{{ item.title }}</span>
                                    </Link>
                                </span>
                            </TooltipTrigger>
                            <TooltipContent side="right" hide-arrow class="border-zinc-700 bg-zinc-800 text-white">
                                {{ item.title }}
                            </TooltipContent>
                        </Tooltip>
                    </nav>
                </div>

                <!-- User -->
                <div
                    :class="[
                        'border-t border-zinc-800 p-4',
                        collapsed ? 'px-2' : '',
                    ]"
                >
                    <Tooltip :disabled="!collapsed">
                        <TooltipTrigger as-child>
                            <div
                                :class="[
                                    'flex items-center rounded-lg px-3 py-2',
                                    collapsed ? 'justify-center px-0' : 'gap-3',
                                ]"
                            >
                                <Avatar class="size-9 shrink-0 border-2 border-zinc-700">
                                    <AvatarFallback class="bg-indigo-600 text-sm font-medium text-white">
                                        {{ user?.name?.charAt(0) ?? 'A' }}
                                    </AvatarFallback>
                                </Avatar>
                                <div v-show="!collapsed" class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-white">{{ user?.name }}</p>
                                    <p class="truncate text-xs text-zinc-400">{{ user?.email }}</p>
                                </div>
                            </div>
                        </TooltipTrigger>
                        <TooltipContent side="right" hide-arrow class="border-zinc-700 bg-zinc-800 text-white">
                            <p class="font-medium">{{ user?.name }}</p>
                            <p class="text-zinc-400">{{ user?.email }}</p>
                        </TooltipContent>
                    </Tooltip>
                </div>
            </aside>

            <!-- Main content area -->
            <div
                :class="[
                    'flex flex-1 flex-col transition-[padding] duration-200',
                    collapsed ? 'md:pl-16' : 'md:pl-64',
                ]"
            >
            <!-- Topbar -->
            <header
                class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-4 border-b border-zinc-200/80 bg-white/95 px-4 backdrop-blur-sm md:px-6"
            >
                <!-- Mobile menu -->
                <Sheet>
                    <SheetTrigger as-child>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="md:hidden"
                        >
                            <Menu class="size-5" />
                        </Button>
                    </SheetTrigger>
                    <SheetContent side="left" class="w-64 max-w-[16rem] border-zinc-800 bg-zinc-900 p-0 [&>button]:hidden">
                        <div class="flex h-16 items-center gap-3 border-b border-zinc-800 px-6">
                            <div
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-600 text-white"
                            >
                                <LayoutDashboard class="size-5" />
                            </div>
                            <span class="font-semibold text-white">ReadyMind</span>
                        </div>
                        <nav class="flex flex-col gap-1 p-4">
                            <Link
                                v-for="item in navItems"
                                :key="item.href"
                                :href="item.href"
                                :class="[
                                    'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                                    isActive(item.href)
                                        ? 'bg-indigo-600 text-white'
                                        : 'text-zinc-300 hover:bg-zinc-800 hover:text-white',
                                ]"
                            >
                                <component :is="item.icon" class="size-5 shrink-0" />
                                {{ item.title }}
                            </Link>
                        </nav>
                    </SheetContent>
                </Sheet>

                <h1 class="flex-1 text-lg font-semibold text-zinc-900">
                    {{ pageTitle }}
                </h1>

                <!-- User menu -->
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <button
                            type="button"
                            class="flex items-center gap-3 rounded-full border border-zinc-200 bg-white py-1.5 pl-1.5 pr-3 shadow-sm transition-all hover:border-zinc-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:ring-offset-2"
                        >
                            <Avatar class="size-8 border-2 border-white shadow-sm">
                                <AvatarFallback class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-sm font-semibold text-white">
                                    {{ user?.name?.charAt(0) ?? 'A' }}
                                </AvatarFallback>
                            </Avatar>
                            <div class="hidden flex-col items-start text-left md:flex">
                                <span class="text-sm font-medium text-zinc-900">{{ user?.name }}</span>
                                <span class="text-xs text-zinc-500">{{ user?.email }}</span>
                            </div>
                            <ChevronDown class="size-4 shrink-0 text-zinc-400 transition-transform [[data-state=open]_&]:rotate-180" />
                        </button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                        align="end"
                        class="w-48 rounded-lg border border-zinc-200 bg-white p-1 shadow-md"
                    >
                        <DropdownMenuItem as-child>
                            <Link
                                :href="route('admin.profile.edit')"
                                class="flex cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 text-sm hover:bg-zinc-100"
                            >
                                <User class="size-4 text-zinc-500" />
                                <span>Mi perfil</span>
                            </Link>
                        </DropdownMenuItem>
                        <DropdownMenuSeparator class="my-1" />
                        <DropdownMenuItem as-child>
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="flex w-full cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 text-sm text-red-600 hover:bg-red-50 focus:bg-red-50 focus:text-red-700"
                            >
                                <LogOut class="size-4" />
                                <span>Cerrar sesión</span>
                            </Link>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-auto bg-zinc-50 p-4 md:p-6">
                <div class="mx-auto max-w-7xl">
                    <slot />
                </div>
            </main>
        </div>
    </div>
    </TooltipProvider>
</template>
