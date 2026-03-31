<script setup>
import { Link } from '@inertiajs/vue3';
import { LayoutDashboard } from 'lucide-vue-next';
</script>

<template>
    <div class="relative min-h-screen overflow-hidden bg-zinc-950 text-zinc-100">
        <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_80%_50%_at_50%_-20%,rgba(99,102,241,0.28),transparent)]"
        />
        <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_50%_40%_at_100%_80%,rgba(139,92,246,0.1),transparent)]"
        />
        <div
            class="pointer-events-none absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-indigo-500/35 to-transparent"
        />

        <div
            class="relative flex min-h-screen flex-col items-center justify-center px-4 py-10 sm:px-6 sm:py-12"
        >
            <Link
                href="/"
                class="group mb-8 flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-sm transition hover:border-indigo-500/30 hover:bg-white/10"
            >
                <div
                    class="flex size-11 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 shadow-lg shadow-indigo-500/30 transition group-hover:shadow-indigo-500/40"
                >
                    <LayoutDashboard class="size-5 text-white" />
                </div>
                <div class="text-left">
                    <span class="block text-base font-bold tracking-tight text-white">ReadyMind</span>
                    <span class="text-xs text-zinc-400">Partner Portal</span>
                </div>
            </Link>

            <div
                class="w-full max-w-md rounded-2xl border border-zinc-200/15 bg-white/[0.97] p-8 shadow-2xl shadow-black/20 backdrop-blur-xl sm:p-9 dark:border-zinc-800 dark:bg-zinc-900/95"
            >
                <slot />
            </div>

            <p class="mt-8 text-center text-sm text-zinc-500">
                <Link href="/" class="font-medium text-zinc-400 transition hover:text-indigo-300">
                    ← Volver al inicio
                </Link>
            </p>
        </div>
    </div>
</template>
