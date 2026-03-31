<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ArrowLeft } from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps<{
    customer: {
        id: number;
        tenant_id: number;
        name: string;
        email: string;
        microsoft_tenant_id: string | null;
        is_active: boolean;
        external_id: string | null;
    };
    tenants: Array<{ id: number; name: string; code: string }>;
}>();

const form = useForm({
    tenant_id: String(props.customer.tenant_id),
    name: props.customer.name,
    email: props.customer.email,
    microsoft_tenant_id: props.customer.microsoft_tenant_id ?? '',
    password: '',
    is_active: props.customer.is_active,
});

const submit = () => {
    form.patch(`/admin/customers/${props.customer.id}`);
};

const regenerateCredentials = () => {
    form.post(`/admin/customers/${props.customer.id}/regenerate-credentials`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Edit Customer" />

    <AdminLayout>
        <div class="mx-auto max-w-3xl space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-zinc-900">Edit Customer</h2>
                    <p class="mt-1 text-sm text-zinc-500">
                        External ID: <span class="font-mono">{{ customer.external_id }}</span>
                    </p>
                </div>
                <Button variant="outline" size="sm" class="h-9 gap-2 rounded-lg" as-child>
                    <Link href="/admin/customers" class="inline-flex items-center gap-2 text-zinc-700">
                        <ArrowLeft class="size-4" />
                        Volver al listado
                    </Link>
                </Button>
            </div>

            <Card class="border-zinc-200">
                <CardHeader>
                    <CardTitle class="text-base">Customer data</CardTitle>
                    <CardDescription>Update access and tenant assignment.</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <Label for="tenant_id">Tenant</Label>
                        <select
                            id="tenant_id"
                            v-model="form.tenant_id"
                            class="mt-1 block w-full rounded-md border-zinc-300 text-sm"
                        >
                            <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">
                                {{ tenant.name }} ({{ tenant.code }})
                            </option>
                        </select>
                        <p class="mt-1 text-sm text-red-600">{{ form.errors.tenant_id }}</p>
                    </div>

                    <div>
                        <Label for="name">Name</Label>
                        <Input id="name" v-model="form.name" class="mt-1" />
                        <p class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <Label for="email">Email</Label>
                        <Input id="email" type="email" v-model="form.email" class="mt-1" />
                        <p class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <Label for="microsoft_tenant_id">Microsoft Tenant ID</Label>
                        <Input id="microsoft_tenant_id" v-model="form.microsoft_tenant_id" class="mt-1" />
                        <p class="mt-1 text-sm text-red-600">{{ form.errors.microsoft_tenant_id }}</p>
                    </div>

                    <div>
                        <Label for="password">New password (optional)</Label>
                        <Input id="password" type="text" v-model="form.password" class="mt-1" />
                        <p class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-zinc-700">
                        <input v-model="form.is_active" type="checkbox" class="rounded border-zinc-300" />
                        Customer active
                    </label>

                    <div class="flex flex-wrap justify-end gap-2">
                        <Button
                            variant="outline"
                            class="rounded-lg"
                            :disabled="form.processing"
                            @click="regenerateCredentials"
                        >
                            Regenerar credenciales
                        </Button>
                        <Button
                            class="rounded-lg bg-indigo-600 font-semibold text-white shadow-md shadow-indigo-600/20 hover:bg-indigo-700 hover:text-white focus-visible:text-white"
                            :disabled="form.processing"
                            @click="submit"
                        >
                            Guardar cambios
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
