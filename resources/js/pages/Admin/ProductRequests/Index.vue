<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Inbox } from 'lucide-vue-next';

defineProps<{
    productRequests: {
        data: Array<{
            id: number;
            product_name: string | null;
            quantity: number | null;
            product_description: string;
            status: string;
            notified_at: string | null;
            customer: { name: string; tenant?: { name: string } };
            created_at: string;
        }>;
    };
}>();

const statusVariant = (status: string) => {
    switch (status) {
        case 'completed':
            return 'success';
        case 'in_progress':
            return 'warning';
        default:
            return 'outline';
    }
};
</script>

<template>
    <Head title="Contact Requests" />

    <AdminLayout>
        <div class="space-y-6">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-zinc-900">
                    Solicitudes de contacto
                </h2>
                <p class="mt-1 text-sm text-zinc-500">
                    Mensajes enviados desde el portal cuando un cliente pide productos nuevos.
                </p>
            </div>

            <Card class="border-zinc-200 shadow-sm">
                <CardHeader class="border-b border-zinc-100 bg-zinc-50/50">
                    <div class="flex items-center gap-3">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-zinc-200">
                            <Inbox class="size-4 text-violet-600" />
                        </span>
                        <div>
                            <CardTitle class="text-base">Todas las solicitudes</CardTitle>
                            <CardDescription>Formulario «Solicitar productos nuevos» del portal.</CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow class="border-zinc-200 hover:bg-transparent">
                                <TableHead class="font-medium text-zinc-600">Customer</TableHead>
                                <TableHead class="font-medium text-zinc-600">Tenant</TableHead>
                                <TableHead class="font-medium text-zinc-600">Product</TableHead>
                                <TableHead class="font-medium text-zinc-600">Qty</TableHead>
                                <TableHead class="font-medium text-zinc-600">Message</TableHead>
                                <TableHead class="font-medium text-zinc-600">Status</TableHead>
                                <TableHead class="font-medium text-zinc-600">Notified</TableHead>
                                <TableHead class="font-medium text-zinc-600">Date</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="req in productRequests.data"
                                :key="req.id"
                                class="border-zinc-100"
                            >
                                <TableCell class="font-medium text-zinc-900">
                                    {{ req.customer?.name }}
                                </TableCell>
                                <TableCell class="text-zinc-600">
                                    {{ req.customer?.tenant?.name }}
                                </TableCell>
                                <TableCell class="text-zinc-600">
                                    {{ req.product_name ?? '-' }}
                                </TableCell>
                                <TableCell class="text-zinc-600">
                                    {{ req.quantity ?? '-' }}
                                </TableCell>
                                <TableCell class="max-w-xs truncate text-zinc-600">
                                    {{ req.product_description }}
                                </TableCell>
                                <TableCell>
                                    <Badge :variant="statusVariant(req.status)">
                                        {{ req.status }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-zinc-600">
                                    {{ req.notified_at ? 'Yes' : 'No' }}
                                </TableCell>
                                <TableCell class="text-zinc-600">
                                    {{ new Date(req.created_at).toLocaleDateString() }}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
