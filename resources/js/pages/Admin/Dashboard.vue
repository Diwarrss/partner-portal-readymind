<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Building2, Users, Package, ArrowRight } from 'lucide-vue-next';

const props = withDefaults(
    defineProps<{
        stats?: {
            tenants: number;
            customers: number;
            productRequests: number;
        };
    }>(),
    { stats: () => ({ tenants: 0, customers: 0, productRequests: 0 }) }
);

const statCards = [
    {
        title: 'Tenants',
        value: props.stats?.tenants ?? 0,
        description: 'Organizations',
        href: '/admin/tenants',
        icon: Building2,
        color: 'bg-blue-500',
        light: 'bg-blue-50',
        text: 'text-blue-600',
    },
    {
        title: 'Customers',
        value: props.stats?.customers ?? 0,
        description: 'Total accounts',
        href: '/admin/customers',
        icon: Users,
        color: 'bg-emerald-500',
        light: 'bg-emerald-50',
        text: 'text-emerald-600',
    },
    {
        title: 'Product Requests',
        value: props.stats?.productRequests ?? 0,
        description: 'Pending & completed',
        href: '/admin/product-requests',
        icon: Package,
        color: 'bg-amber-500',
        light: 'bg-amber-50',
        text: 'text-amber-600',
    },
];
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <div class="space-y-8">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-zinc-900">
                    Overview
                </h2>
                <p class="mt-1 text-sm text-zinc-500">
                    Welcome back. Here's what's happening in your portal.
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="stat in statCards"
                    :key="stat.href"
                    :href="stat.href"
                    class="group"
                >
                    <Card
                        class="overflow-hidden border-zinc-200 transition-all hover:border-zinc-300 hover:shadow-md"
                    >
                        <CardHeader class="flex flex-row items-center justify-between pb-2">
                            <CardTitle class="text-sm font-medium text-zinc-500">
                                {{ stat.title }}
                            </CardTitle>
                            <div
                                :class="[
                                    'flex size-10 items-center justify-center rounded-lg',
                                    stat.light,
                                    stat.text,
                                ]"
                            >
                                <component :is="stat.icon" class="size-5" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-zinc-900">
                                {{ stat.value.toLocaleString() }}
                            </div>
                            <p class="mt-1 flex items-center gap-1 text-xs text-zinc-500">
                                {{ stat.description }}
                                <ArrowRight
                                    class="size-3.5 opacity-0 transition-opacity group-hover:opacity-100"
                                />
                            </p>
                        </CardContent>
                    </Card>
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>
