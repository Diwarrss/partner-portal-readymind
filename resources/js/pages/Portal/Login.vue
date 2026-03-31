<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});
</script>

<template>
    <Head title="Iniciar sesión - Portal" />

    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-md space-y-8 rounded-lg bg-white p-8 shadow">
            <div>
                <h1 class="text-center text-2xl font-bold text-gray-900">
                    Portal de Clientes
                </h1>
                <p class="mt-2 text-center text-sm text-gray-600">
                    ReadyMind - Gestión de licencias
                </p>
            </div>

            <form @submit.prevent="form.post('/portal/login')" class="mt-8 space-y-6">
                <div class="space-y-4">
                    <div>
                        <Label for="email">Correo electrónico</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            required
                            class="mt-1"
                            :class="{ 'border-red-500': form.errors.email }"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div>
                        <Label for="password">Contraseña</Label>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="mt-1"
                            :class="{ 'border-red-500': form.errors.password }"
                        />
                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="flex items-center">
                        <input
                            id="remember"
                            v-model="form.remember"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300"
                        />
                        <Label for="remember" class="ml-2">Recordarme</Label>
                    </div>
                </div>

                <Button
                    type="submit"
                    class="w-full"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Iniciando...' : 'Iniciar sesión' }}
                </Button>
            </form>

            <p class="text-center text-sm text-gray-600">
                ¿Eres administrador?
                <Link href="/admin" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Acceder al panel admin
                </Link>
            </p>
        </div>
    </div>
</template>
