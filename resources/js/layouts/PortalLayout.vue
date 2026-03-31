<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { LayoutDashboard, LogOut } from 'lucide-vue-next';

const page = usePage();
const customer = computed(() => page.props.auth?.customer ?? null);
</script>

<template>
    <div class="min-h-screen bg-gradient-to-b from-zinc-100 to-zinc-50">
        <header class="sticky top-0 z-40 border-b border-zinc-200/80 bg-white/90 shadow-sm backdrop-blur-md">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <div class="flex min-w-0 items-center gap-8">
                    <Link
                        href="/portal/dashboard"
                        class="group flex items-center gap-2.5"
                    >
                        <span
                            class="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-600/25 transition group-hover:shadow-lg"
                        >
                            <LayoutDashboard class="size-4" />
                        </span>
                        <span class="hidden font-semibold tracking-tight text-zinc-900 sm:block">
                            ReadyMind
                            <span class="block text-xs font-normal text-zinc-500">Portal de clientes</span>
                        </span>
                    </Link>
                    <nav class="hidden sm:flex sm:items-center sm:gap-1">
                        <Link
                            href="/portal/dashboard"
                            class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900"
                            :class="{
                                'bg-indigo-50 text-indigo-800': $page.url.startsWith('/portal/dashboard'),
                            }"
                        >
                            Licencias
                        </Link>
                    </nav>
                </div>
                <div class="flex shrink-0 items-center gap-3">
                    <div class="hidden text-right text-sm sm:block">
                        <p class="truncate font-medium text-zinc-900">{{ customer?.name ?? '' }}</p>
                        <p v-if="customer?.tenant?.code" class="text-xs text-zinc-500">
                            Centro <span class="font-mono font-semibold text-indigo-700">{{ customer.tenant.code }}</span>
                        </p>
                    </div>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="inline-flex h-9 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 shadow-sm transition hover:border-zinc-300 hover:bg-zinc-50"
                    >
                        <LogOut class="size-4 text-zinc-500" />
                        <span class="hidden sm:inline">Salir</span>
                    </Link>
                </div>
            </div>
        </header>

        <main class="py-8 sm:py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>
    </div>
</template>
