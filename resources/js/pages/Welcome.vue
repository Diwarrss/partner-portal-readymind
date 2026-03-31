<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
    ArrowRight,
    Building2,
    LayoutDashboard,
    LogIn,
    Package,
    Shield,
    Sparkles,
    UserPlus,
    Users,
} from 'lucide-vue-next';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
});
</script>

<template>
    <Head title="ReadyMind — Partner Portal" />

    <div class="relative min-h-screen overflow-hidden bg-zinc-950 text-zinc-100">
        <!-- Background -->
        <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_80%_50%_at_50%_-20%,rgba(99,102,241,0.35),transparent)]"
        />
        <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_60%_40%_at_100%_50%,rgba(139,92,246,0.12),transparent)]"
        />
        <div
            class="pointer-events-none absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-indigo-500/40 to-transparent"
        />

        <div class="relative mx-auto flex min-h-screen max-w-6xl flex-col px-4 sm:px-6 lg:px-8">
            <!-- Nav -->
            <header class="flex h-16 items-center justify-between sm:h-20">
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 shadow-lg shadow-indigo-500/25"
                    >
                        <LayoutDashboard class="size-5 text-white" />
                    </div>
                    <div>
                        <span class="text-lg font-bold tracking-tight text-white">ReadyMind</span>
                        <span class="hidden text-xs text-zinc-500 sm:block">Partner Portal</span>
                    </div>
                </div>

                <nav v-if="canLogin" class="flex flex-wrap items-center justify-end gap-2 sm:gap-3">
                    <template v-if="$page.props.auth?.user">
                        <Button variant="ghost" class="text-zinc-300 hover:bg-white/10 hover:text-white" as-child>
                            <Link :href="route('admin.dashboard')" class="inline-flex items-center gap-2">
                                <Shield class="size-4" />
                                <span class="hidden sm:inline">Panel admin</span>
                                <span class="sm:hidden">Admin</span>
                            </Link>
                        </Button>
                    </template>
                    <template v-else-if="$page.props.auth?.customer">
                        <Button variant="ghost" class="text-zinc-300 hover:bg-white/10 hover:text-white" as-child>
                            <Link :href="route('portal.dashboard')" class="inline-flex items-center gap-2">
                                <Package class="size-4" />
                                Mi portal
                            </Link>
                        </Button>
                    </template>
                    <template v-else>
                        <Button
                            class="h-10 gap-2 rounded-xl border-0 bg-indigo-600 px-4 text-sm font-semibold text-white shadow-lg shadow-indigo-600/35 hover:bg-indigo-500"
                            as-child
                        >
                            <Link :href="route('login')" class="inline-flex items-center gap-2">
                                <LogIn class="size-4 shrink-0 opacity-90" />
                                Iniciar sesión
                            </Link>
                        </Button>
                        <Button
                            v-if="canRegister"
                            variant="outline"
                            class="h-10 gap-2 rounded-xl border-2 border-white/35 bg-white/10 px-4 text-sm font-semibold text-white shadow-sm backdrop-blur-sm hover:bg-white/20 hover:text-white"
                            as-child
                        >
                            <Link :href="route('register')" class="inline-flex items-center gap-2">
                                <UserPlus class="size-4 shrink-0 opacity-90" />
                                Registrarse
                            </Link>
                        </Button>
                    </template>
                </nav>
            </header>

            <!-- Hero -->
            <main class="flex flex-1 flex-col justify-center py-12 sm:py-16 lg:py-20">
                <div class="mx-auto max-w-3xl text-center">
                    <div
                        class="mb-6 inline-flex items-center gap-2 rounded-full border border-indigo-500/30 bg-indigo-500/10 px-4 py-1.5 text-xs font-medium text-indigo-200 backdrop-blur-sm"
                    >
                        <Sparkles class="size-3.5 text-indigo-400" />
                        Integración Microsoft Partner Center
                    </div>
                    <h1
                        class="text-balance text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl lg:leading-[1.1]"
                    >
                        Un solo portal para
                        <span class="bg-gradient-to-r from-indigo-300 via-violet-300 to-indigo-200 bg-clip-text text-transparent">
                            partners y clientes
                        </span>
                    </h1>
                    <p class="mx-auto mt-6 max-w-xl text-pretty text-base leading-relaxed text-zinc-400 sm:text-lg">
                        Gestiona tenants, licencias y solicitudes conectadas a Microsoft. Acceso unificado para tu equipo
                        interno y para las organizaciones que atiendes.
                    </p>
                    <div
                        v-if="canLogin && !$page.props.auth?.user && !$page.props.auth?.customer"
                        class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row sm:gap-4"
                    >
                        <Button
                            size="lg"
                            class="h-12 min-w-[200px] rounded-xl bg-indigo-600 px-8 text-base font-semibold text-white shadow-xl shadow-indigo-600/30 hover:bg-indigo-500 hover:text-white focus-visible:text-white"
                            as-child
                        >
                            <Link
                                :href="route('login')"
                                class="inline-flex items-center gap-2 text-white no-underline hover:text-white"
                            >
                                Acceder al portal
                                <ArrowRight class="size-5 shrink-0 text-white" />
                            </Link>
                        </Button>
                        <p class="max-w-xs text-center text-sm text-zinc-500 sm:text-left">
                            Mismo inicio de sesión para <strong class="text-zinc-400">administradores</strong> y
                            <strong class="text-zinc-400">clientes</strong> (elige tu centro si eres cliente).
                        </p>
                    </div>
                </div>

                <!-- Feature cards -->
                <div class="mx-auto mt-16 grid max-w-5xl gap-4 sm:grid-cols-3 sm:gap-5 lg:mt-20">
                    <div
                        class="group rounded-2xl border border-white/10 bg-white/[0.03] p-6 backdrop-blur-sm transition hover:border-indigo-500/30 hover:bg-white/[0.06]"
                    >
                        <div
                            class="mb-4 flex size-11 items-center justify-center rounded-xl bg-indigo-500/15 text-indigo-400 ring-1 ring-indigo-500/20"
                        >
                            <Shield class="size-5" />
                        </div>
                        <h2 class="text-lg font-semibold text-white">Panel administrativo</h2>
                        <p class="mt-2 text-sm leading-relaxed text-zinc-500">
                            Tenants, clientes, credenciales Partner Center y exportación de licencias en un solo lugar.
                        </p>
                    </div>
                    <div
                        class="group rounded-2xl border border-white/10 bg-white/[0.03] p-6 backdrop-blur-sm transition hover:border-violet-500/30 hover:bg-white/[0.06]"
                    >
                        <div
                            class="mb-4 flex size-11 items-center justify-center rounded-xl bg-violet-500/15 text-violet-400 ring-1 ring-violet-500/20"
                        >
                            <Users class="size-5" />
                        </div>
                        <h2 class="text-lg font-semibold text-white">Portal de clientes</h2>
                        <p class="mt-2 text-sm leading-relaxed text-zinc-500">
                            Consulta de suscripciones, ajuste de cantidades cuando aplique y solicitud de productos
                            nuevos.
                        </p>
                    </div>
                    <div
                        class="group rounded-2xl border border-white/10 bg-white/[0.03] p-6 backdrop-blur-sm transition hover:border-indigo-500/30 hover:bg-white/[0.06] sm:col-span-1"
                    >
                        <div
                            class="mb-4 flex size-11 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400 ring-1 ring-emerald-500/20"
                        >
                            <Building2 class="size-5" />
                        </div>
                        <h2 class="text-lg font-semibold text-white">Datos desde Microsoft</h2>
                        <p class="mt-2 text-sm leading-relaxed text-zinc-500">
                            Sincronización con Partner Center: licencias reales según la configuración de cada región
                            (sandbox o producción).
                        </p>
                    </div>
                </div>
            </main>

            <footer class="border-t border-white/10 py-8 text-center sm:flex sm:items-center sm:justify-between sm:text-left">
                <p class="text-sm text-zinc-500">
                    © {{ new Date().getFullYear() }}
                    <a
                        href="https://readymind.ms"
                        class="font-medium text-zinc-400 underline-offset-4 hover:text-indigo-300 hover:underline"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        readymind.ms
                    </a>
                    · Herramienta interna de partner
                </p>
                <p class="mt-2 text-xs text-zinc-600 sm:mt-0">
                    Ambiente local · Laravel + Inertia + Vue
                </p>
            </footer>
        </div>
    </div>
</template>
