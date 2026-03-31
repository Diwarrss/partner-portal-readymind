<script setup>
import GuestLayout from '@/layouts/GuestLayout.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, UserPlus } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Crear cuenta" />

        <div class="mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
                Crear cuenta
            </h1>
            <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                Registro para usuarios del portal interno. Los clientes del portal suelen ser dados de alta por un
                administrador.
            </p>
        </div>

        <form class="space-y-5" @submit.prevent="submit">
            <div class="space-y-2">
                <Label for="name" class="text-zinc-700 dark:text-zinc-300">Nombre completo</Label>
                <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    class="h-11 rounded-xl border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-950"
                />
                <InputError :message="form.errors.name" />
            </div>

            <div class="space-y-2">
                <Label for="email" class="text-zinc-700 dark:text-zinc-300">Correo electrónico</Label>
                <Input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    autocomplete="username"
                    placeholder="tu-correo@empresa.ms"
                    class="h-11 rounded-xl border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-950"
                />
                <InputError :message="form.errors.email" />
            </div>

            <div class="space-y-2">
                <Label for="password" class="text-zinc-700 dark:text-zinc-300">Contraseña</Label>
                <Input
                    id="password"
                    v-model="form.password"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="h-11 rounded-xl border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-950"
                />
                <InputError :message="form.errors.password" />
            </div>

            <div class="space-y-2">
                <Label for="password_confirmation" class="text-zinc-700 dark:text-zinc-300">
                    Confirmar contraseña
                </Label>
                <Input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="h-11 rounded-xl border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-950"
                />
                <InputError :message="form.errors.password_confirmation" />
            </div>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <Link
                    :href="route('login')"
                    class="order-2 inline-flex items-center justify-center gap-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 sm:order-1 dark:text-zinc-400 dark:hover:text-white"
                >
                    <ArrowLeft class="size-4" />
                    Ya tengo cuenta
                </Link>
                <Button
                    type="submit"
                    class="order-1 h-11 w-full gap-2 rounded-xl bg-indigo-600 text-base font-semibold text-white shadow-lg shadow-indigo-600/25 hover:bg-indigo-500 hover:text-white focus-visible:text-white sm:order-2 sm:w-auto sm:min-w-[180px]"
                    :disabled="form.processing"
                >
                    <UserPlus v-if="!form.processing" class="size-4 shrink-0 text-white" />
                    <span>{{ form.processing ? 'Creando…' : 'Registrarme' }}</span>
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
