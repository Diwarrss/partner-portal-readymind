<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Building2, Pencil, Plus } from 'lucide-vue-next';

defineProps<{
    tenants: {
        data: Array<{
            id: number;
            code: string;
            name: string;
            is_sandbox: boolean;
            partner_center_configured: boolean;
            customers_count: number;
        }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
}>();

const page = usePage();
const flash = page.props.flash as { success?: string; error?: string } | undefined;
const authUser = page.props.auth?.user as { tenant_id?: number | null } | undefined;
const canManageTenants = !authUser?.tenant_id;
</script>

<template>
    <Head title="Tenants" />

    <AdminLayout>
        <div class="space-y-6">
            <div v-if="flash?.success" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ flash.success }}
            </div>
            <div v-if="flash?.error" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ flash.error }}
            </div>

            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-zinc-900">Tenants</h2>
                    <p class="mt-1 text-sm text-zinc-500">
                        Regiones / centros y credenciales Partner Center para traer datos desde Microsoft.
                    </p>
                </div>
                <Button
                    v-if="canManageTenants"
                    size="sm"
                    class="h-9 gap-2 rounded-lg bg-indigo-600 font-semibold text-white shadow-md shadow-indigo-600/20 hover:bg-indigo-700 hover:text-white focus-visible:text-white"
                    as-child
                >
                    <Link
                        href="/admin/tenants/create"
                        class="inline-flex items-center gap-2 text-white no-underline hover:text-white"
                    >
                        <Plus class="size-4 shrink-0 text-white" />
                        Nuevo tenant
                    </Link>
                </Button>
            </div>

            <Card class="border-zinc-200 shadow-sm">
                <CardHeader class="border-b border-zinc-100 bg-zinc-50/50">
                    <div class="flex items-center gap-2">
                        <span class="flex size-9 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-zinc-200">
                            <Building2 class="size-4 text-indigo-600" />
                        </span>
                        <div>
                            <CardTitle class="text-base">Listado</CardTitle>
                            <CardDescription>Cada cliente se asocia a un tenant (código en el login).</CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow class="border-zinc-200 hover:bg-transparent">
                                <TableHead class="font-medium text-zinc-600">Código</TableHead>
                                <TableHead class="font-medium text-zinc-600">Nombre</TableHead>
                                <TableHead class="font-medium text-zinc-600">Sandbox</TableHead>
                                <TableHead class="font-medium text-zinc-600">Partner Center</TableHead>
                                <TableHead class="font-medium text-zinc-600">Clientes</TableHead>
                                <TableHead class="font-medium text-zinc-600">Acciones</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="tenant in tenants.data"
                                :key="tenant.id"
                                class="border-zinc-100"
                            >
                                <TableCell class="font-medium text-zinc-900">
                                    {{ tenant.code }}
                                </TableCell>
                                <TableCell class="text-zinc-600">{{ tenant.name }}</TableCell>
                                <TableCell class="text-zinc-600">
                                    <span
                                        :class="tenant.is_sandbox ? 'text-amber-700' : 'text-zinc-500'"
                                        class="text-xs font-medium"
                                    >
                                        {{ tenant.is_sandbox ? 'Sí' : 'No' }}
                                    </span>
                                </TableCell>
                                <TableCell class="text-zinc-600">
                                    <span
                                        :class="tenant.partner_center_configured ? 'text-emerald-700' : 'text-zinc-400'"
                                        class="text-xs font-medium"
                                    >
                                        {{ tenant.partner_center_configured ? 'Configurado' : 'Pendiente' }}
                                    </span>
                                </TableCell>
                                <TableCell class="text-zinc-600">
                                    {{ tenant.customers_count }}
                                </TableCell>
                                <TableCell>
                                    <Button variant="secondary" size="sm" class="h-8 gap-1.5 rounded-lg px-3" as-child>
                                        <Link
                                            :href="`/admin/tenants/${tenant.id}/edit`"
                                            class="inline-flex items-center gap-1.5"
                                        >
                                            <Pencil class="size-3.5 shrink-0 text-zinc-500" />
                                            Editar
                                        </Link>
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
