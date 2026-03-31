<script setup>
import GuestLayout from '@/layouts/GuestLayout.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Mail } from 'lucide-vue-next';

defineProps({
    status: { type: String },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Recuperar contraseña" />

        <div class="mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
                ¿Olvidaste tu contraseña?
            </h1>
            <p class="mt-3 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                Indica el correo con el que te registraste. Te enviaremos un enlace para elegir una contraseña nueva.
                Revisa también la carpeta de spam.
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

            <div class="flex flex-col gap-3 pt-2">
                <Button
                    type="submit"
                    class="h-12 w-full gap-2 rounded-xl bg-indigo-600 text-base font-semibold text-white shadow-lg shadow-indigo-600/25 hover:bg-indigo-500 hover:text-white focus-visible:text-white"
                    :disabled="form.processing"
                >
                    <Mail v-if="!form.processing" class="size-4 shrink-0 text-white" />
                    <span class="text-center leading-snug">{{
                        form.processing ? 'Enviando…' : 'Enviar enlace de recuperación'
                    }}</span>
                </Button>
                <Button
                    variant="outline"
                    class="h-12 w-full rounded-xl border-zinc-300 bg-white text-base font-medium text-zinc-700 shadow-sm hover:bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-950 dark:text-zinc-200 dark:hover:bg-zinc-900"
                    as-child
                >
                    <Link
                        :href="route('login')"
                        class="inline-flex w-full items-center justify-center gap-2"
                    >
                        <ArrowLeft class="size-4 shrink-0" />
                        Volver al inicio de sesión
                    </Link>
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
