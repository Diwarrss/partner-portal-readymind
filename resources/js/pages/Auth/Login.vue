<script setup>
import GuestLayout from '@/layouts/GuestLayout.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowRight, LogIn } from 'lucide-vue-next';

defineProps({
    canResetPassword: { type: Boolean },
    canRegister: { type: Boolean, default: false },
    status: { type: String },
    tenants: { type: Array, default: () => [] },
});

const form = useForm({
    email: '',
    password: '',
    tenant_code: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Iniciar sesión" />

        <div class="mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
                Iniciar sesión
            </h1>
            <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                Un solo acceso para administradores y clientes. Si eres cliente, elige tu centro (tenant).
            </p>
        </div>

        <div
            v-if="status"
            class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/50 dark:text-emerald-200"
        >
            {{ status }}
        </div>

        <form class="space-y-5" @submit.prevent="submit">
            <div class="space-y-2">
                <Label for="email" class="text-zinc-700 dark:text-zinc-300">Correo electrónico</Label>
                <Input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="tu-correo@empresa.ms"
                    class="h-11 rounded-xl border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-950"
                />
                <InputError :message="form.errors.email" />
            </div>

            <div class="space-y-2">
                <Label for="tenant_code" class="text-zinc-700 dark:text-zinc-300">
                    Centro <span class="font-normal text-zinc-500">(solo clientes)</span>
                </Label>
                <select
                    id="tenant_code"
                    v-model="form.tenant_code"
                    class="flex h-11 w-full rounded-xl border border-zinc-200 bg-white px-3 text-sm text-zinc-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                >
                    <option value="">Opcional — obligatorio para clientes</option>
                    <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.code">
                        {{ tenant.name }} ({{ tenant.code }})
                    </option>
                </select>
                <InputError :message="form.errors.tenant_code" />
            </div>

            <div class="space-y-2">
                <Label for="password" class="text-zinc-700 dark:text-zinc-300">Contraseña</Label>
                <Input
                    id="password"
                    v-model="form.password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="h-11 rounded-xl border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-950"
                />
                <InputError :message="form.errors.password" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="remember"
                    :checked="form.remember"
                    class="border-zinc-300 data-[state=checked]:border-indigo-600 data-[state=checked]:bg-indigo-600"
                    @update:checked="(v) => (form.remember = !!v)"
                />
                <Label
                    for="remember"
                    class="cursor-pointer text-sm font-normal text-zinc-600 dark:text-zinc-400"
                >
                    Recordarme en este equipo
                </Label>
            </div>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="order-2 text-sm font-medium text-indigo-600 hover:text-indigo-500 sm:order-1 dark:text-indigo-400 dark:hover:text-indigo-300"
                >
                    ¿Olvidaste tu contraseña?
                </Link>
                <Button
                    type="submit"
                    class="order-1 h-11 w-full gap-2 rounded-xl bg-indigo-600 text-base font-semibold text-white shadow-lg shadow-indigo-600/25 hover:bg-indigo-500 hover:text-white focus-visible:text-white sm:order-2 sm:w-auto sm:min-w-[160px]"
                    :disabled="form.processing"
                >
                    <LogIn v-if="!form.processing" class="size-4 shrink-0 text-white" />
                    <span>{{ form.processing ? 'Entrando…' : 'Entrar' }}</span>
                </Button>
            </div>
        </form>

        <div
            v-if="canRegister"
            class="mt-8 border-t border-zinc-200 pt-6 text-center text-sm text-zinc-600 dark:border-zinc-800 dark:text-zinc-400"
        >
            ¿No tienes cuenta?
            <Link
                :href="route('register')"
                class="ms-1 inline-flex items-center gap-1 font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400"
            >
                Crear cuenta
                <ArrowRight class="size-3.5" />
            </Link>
        </div>
    </GuestLayout>
</template>
