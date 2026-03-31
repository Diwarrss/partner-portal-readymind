<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps<{
    tenant: {
        id: number;
        code: string;
        name: string;
        partner_center_client_id: string | null;
        partner_center_tenant_id: string | null;
        is_sandbox: boolean;
        has_partner_center_client_secret: boolean;
    };
    canDeleteTenant?: boolean;
}>();

const form = useForm({
    code: props.tenant.code,
    name: props.tenant.name,
    partner_center_client_id: props.tenant.partner_center_client_id ?? '',
    partner_center_client_secret: '',
    partner_center_tenant_id: props.tenant.partner_center_tenant_id ?? '',
    is_sandbox: props.tenant.is_sandbox,
});

const submit = () => {
    form.patch(`/admin/tenants/${props.tenant.id}`);
};

const destroyTenant = () => {
    if (!confirm('¿Eliminar este tenant? Solo si no tiene clientes.')) return;
    router.delete(`/admin/tenants/${props.tenant.id}`);
};
</script>

<template>
    <Head title="Edit Tenant" />

    <AdminLayout>
        <div class="mx-auto max-w-3xl space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-zinc-900">Editar tenant</h2>
                    <p class="mt-1 text-sm text-zinc-500">{{ tenant.code }} — {{ tenant.name }}</p>
                </div>
                <Button variant="outline" size="sm" class="h-9 gap-2 rounded-lg" as-child>
                    <Link href="/admin/tenants" class="inline-flex items-center gap-2 text-zinc-700">
                        <ArrowLeft class="size-4" />
                        Volver
                    </Link>
                </Button>
            </div>

            <Card class="border-zinc-200 shadow-sm">
                <CardHeader>
                    <CardTitle class="text-base">Datos del tenant</CardTitle>
                    <CardDescription>Actualiza región y credenciales de Partner Center.</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <Label for="code">Código</Label>
                        <Input id="code" v-model="form.code" class="mt-1" maxlength="10" />
                        <p class="mt-1 text-sm text-red-600">{{ form.errors.code }}</p>
                    </div>

                    <div>
                        <Label for="name">Nombre</Label>
                        <Input id="name" v-model="form.name" class="mt-1" />
                        <p class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 space-y-4">
                        <p class="text-sm font-medium text-zinc-800">Partner Center (Microsoft)</p>
                        <div>
                            <Label for="partner_center_client_id">Client ID</Label>
                            <Input id="partner_center_client_id" v-model="form.partner_center_client_id" class="mt-1 font-mono text-sm" />
                            <p class="mt-1 text-sm text-red-600">{{ form.errors.partner_center_client_id }}</p>
                        </div>
                        <div>
                            <Label for="partner_center_client_secret">Nuevo client secret (opcional)</Label>
                            <Input
                                id="partner_center_client_secret"
                                v-model="form.partner_center_client_secret"
                                type="password"
                                class="mt-1 font-mono text-sm"
                                autocomplete="new-password"
                            />
                            <p v-if="tenant.has_partner_center_client_secret" class="mt-1 text-xs text-zinc-500">
                                Ya hay un secret guardado. Déjalo vacío para no cambiarlo.
                            </p>
                            <p class="mt-1 text-sm text-red-600">{{ form.errors.partner_center_client_secret }}</p>
                        </div>
                        <div>
                            <Label for="partner_center_tenant_id">Directory (tenant) ID</Label>
                            <Input id="partner_center_tenant_id" v-model="form.partner_center_tenant_id" class="mt-1 font-mono text-sm" />
                            <p class="mt-1 text-sm text-red-600">{{ form.errors.partner_center_tenant_id }}</p>
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-zinc-700">
                        <input v-model="form.is_sandbox" type="checkbox" class="rounded border-zinc-300" />
                        Sandbox (marca informativa)
                    </label>

                    <div class="flex flex-wrap justify-between gap-2">
                        <Button
                            v-if="canDeleteTenant"
                            variant="destructive"
                            class="rounded-lg"
                            type="button"
                            @click="destroyTenant"
                        >
                            Eliminar tenant
                        </Button>
                        <div v-else />
                        <div class="flex gap-2">
                            <Button variant="outline" class="rounded-lg" as-child>
                                <Link href="/admin/tenants">Cancelar</Link>
                            </Button>
                            <Button
                                class="rounded-lg bg-indigo-600 font-semibold text-white shadow-md shadow-indigo-600/20 hover:bg-indigo-700 hover:text-white focus-visible:text-white"
                                :disabled="form.processing"
                                type="button"
                                @click="submit"
                            >
                                Guardar
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
