<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
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
import { Download, Pencil, UserPlus } from 'lucide-vue-next';

defineProps<{
    customers: {
        data: Array<{
            id: number;
            name: string;
            email: string;
            is_active: boolean;
            tenant: { name: string };
        }>;
    };
    tenants: Array<{ id: number; name: string; code: string }>;
    filters: { tenant_id?: string | null };
}>();

const applyFilters = (tenantId: string) => {
    router.get('/admin/customers', { tenant_id: tenantId || undefined }, { preserveState: true });
};

const exportCsv = (tenantId: string) => {
    const month = new Date().getMonth() + 1;
    const year = new Date().getFullYear();
    const params = new URLSearchParams({
        month: String(month),
        year: String(year),
    });
    if (tenantId) params.set('tenant_id', tenantId);
    window.location.href = `/admin/export/licenses?${params.toString()}`;
};
</script>

<template>
    <Head title="Customers" />

    <AdminLayout>
        <div class="space-y-6">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-zinc-900">
                    Clientes
                </h2>
                <p class="mt-1 text-sm text-zinc-500">
                    Cuentas del portal, credenciales y vínculo con Microsoft.
                </p>
            </div>

            <Card class="border-zinc-200 shadow-sm">
                <CardHeader class="border-b border-zinc-100 bg-zinc-50/50">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-zinc-200">
                                <UserPlus class="size-4 text-indigo-600" />
                            </span>
                            <div>
                                <CardTitle class="text-base">Clientes</CardTitle>
                                <CardDescription>Cuentas del portal y su tenant asociado.</CardDescription>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <select
                                class="h-9 min-w-[160px] rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                                :value="filters?.tenant_id ?? ''"
                                @change="applyFilters(($event.target as HTMLSelectElement).value)"
                            >
                                <option value="">Todos los tenants</option>
                                <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">
                                    {{ tenant.name }} ({{ tenant.code }})
                                </option>
                            </select>
                            <Button
                                size="sm"
                                variant="outline"
                                class="h-9 gap-2 rounded-lg border-zinc-300 shadow-sm"
                                @click="exportCsv(String(filters?.tenant_id ?? ''))"
                            >
                                <Download class="size-4 text-zinc-500" />
                                Exportar CSV
                            </Button>
                            <Button
                                size="sm"
                                class="h-9 gap-2 rounded-lg bg-indigo-600 font-semibold text-white shadow-md shadow-indigo-600/20 hover:bg-indigo-700 hover:text-white focus-visible:text-white"
                                as-child
                            >
                                <Link
                                    href="/admin/customers/create"
                                    class="inline-flex items-center gap-2 text-white no-underline hover:text-white"
                                >
                                    <UserPlus class="size-4 shrink-0 text-white" />
                                    Nuevo cliente
                                </Link>
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow class="border-zinc-200 hover:bg-transparent">
                                <TableHead class="font-medium text-zinc-600">Name</TableHead>
                                <TableHead class="font-medium text-zinc-600">Email</TableHead>
                                <TableHead class="font-medium text-zinc-600">Tenant</TableHead>
                                <TableHead class="font-medium text-zinc-600">Status</TableHead>
                                <TableHead class="font-medium text-zinc-600">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="customer in customers.data"
                                :key="customer.id"
                                class="border-zinc-100"
                            >
                                <TableCell class="font-medium text-zinc-900">
                                    {{ customer.name }}
                                </TableCell>
                                <TableCell class="text-zinc-600">{{ customer.email }}</TableCell>
                                <TableCell class="text-zinc-600">
                                    {{ customer.tenant?.name }}
                                </TableCell>
                                <TableCell class="text-zinc-600">
                                    {{ customer.is_active ? 'Active' : 'Inactive' }}
                                </TableCell>
                                <TableCell>
                                    <Button variant="secondary" size="sm" class="h-8 gap-1.5 rounded-lg px-3" as-child>
                                        <Link :href="`/admin/customers/${customer.id}/edit`" class="inline-flex items-center gap-1.5">
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
