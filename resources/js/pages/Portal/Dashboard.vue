<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import PortalLayout from '@/layouts/PortalLayout.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import {
    ArrowDownCircle,
    ArrowUpCircle,
    Loader2,
    Mail,
    PackagePlus,
    Sparkles,
} from 'lucide-vue-next';

interface LicenseSubscription {
    id: number;
    offer_id: string;
    offer_name: string;
    quantity: number;
    status: string;
    partner_subscription_id: string;
    cancellation_allowed_until_date: string | null;
}

const props = defineProps<{
    licenses: LicenseSubscription[];
    flash?: { success?: string; error?: string };
}>();

const incrementModal = ref(false);
const decrementModal = ref(false);
const selectedSubscription = ref<LicenseSubscription | null>(null);
const incrementAmount = ref(1);
const decrementAmount = ref(1);
const submitting = ref(false);

const productRequestModal = ref(false);
const productName = ref('');
const requestedQuantity = ref<number | null>(null);
const contactMessage = ref('');

const openIncrementModal = (license: LicenseSubscription) => {
    selectedSubscription.value = license;
    incrementAmount.value = 1;
    incrementModal.value = true;
};

const openDecrementModal = (license: LicenseSubscription) => {
    selectedSubscription.value = license;
    decrementAmount.value = 1;
    decrementModal.value = true;
};

const canDecrease = (license: LicenseSubscription) => {
    if (!license.cancellation_allowed_until_date) return false;
    return new Date(license.cancellation_allowed_until_date) >= new Date();
};

const submitIncrement = () => {
    if (!selectedSubscription.value) return;
    submitting.value = true;
    router.post(
        `/portal/licenses/${selectedSubscription.value.id}/increment`,
        { delta: incrementAmount.value },
        {
            preserveScroll: true,
            onFinish: () => {
                submitting.value = false;
                incrementModal.value = false;
            },
        }
    );
};

const submitDecrement = () => {
    if (!selectedSubscription.value) return;
    submitting.value = true;
    router.post(
        `/portal/licenses/${selectedSubscription.value.id}/decrement`,
        { delta: decrementAmount.value },
        {
            preserveScroll: true,
            onFinish: () => {
                submitting.value = false;
                decrementModal.value = false;
            },
        }
    );
};

const submitContactForm = () => {
    submitting.value = true;
    router.post(
        '/portal/product-requests',
        {
            product_name: productName.value,
            quantity: requestedQuantity.value,
            message: contactMessage.value,
        },
        {
            preserveScroll: true,
            onFinish: () => {
                submitting.value = false;
                productRequestModal.value = false;
                productName.value = '';
                requestedQuantity.value = null;
                contactMessage.value = '';
            },
        }
    );
};

const successMessage = computed(() => props.flash?.success ?? '');
const errorMessage = computed(() => props.flash?.error ?? '');

const statusBadgeClass = (status: string) => {
    const s = status.toLowerCase();
    if (s.includes('active') || s === 'active') {
        return 'border-emerald-200 bg-emerald-50 text-emerald-800 hover:bg-emerald-50';
    }
    if (s.includes('suspend') || s.includes('cancel')) {
        return 'border-amber-200 bg-amber-50 text-amber-900 hover:bg-amber-50';
    }
    return 'border-zinc-200 bg-zinc-50 text-zinc-700 hover:bg-zinc-50';
};
</script>

<template>
    <Head title="Dashboard - Portal" />

    <PortalLayout>
        <div class="space-y-8">
            <!-- Header -->
            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200/80 bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-800">
                        <Sparkles class="size-3.5" />
                        Portal de licencias
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight text-zinc-900">
                        Mis licencias
                    </h1>
                    <p class="max-w-xl text-sm text-zinc-600">
                        Consulta tus suscripciones sincronizadas con Microsoft. Ajusta cantidades cuando aplique o solicita productos nuevos al equipo ReadyMind.
                    </p>
                </div>
                <Button
                    class="h-11 shrink-0 rounded-xl bg-indigo-600 px-5 text-sm font-semibold text-white shadow-md shadow-indigo-600/25 transition hover:bg-indigo-700 hover:text-white focus-visible:text-white"
                    @click="productRequestModal = true"
                >
                    <PackagePlus class="size-4 shrink-0 text-white" />
                    Solicitar productos nuevos
                </Button>
            </div>

            <div v-if="successMessage" class="rounded-xl border border-emerald-200/80 bg-emerald-50/90 px-4 py-3 text-sm font-medium text-emerald-900 shadow-sm">
                {{ successMessage }}
            </div>
            <div v-if="errorMessage" class="rounded-xl border border-red-200/80 bg-red-50/90 px-4 py-3 text-sm font-medium text-red-900 shadow-sm">
                {{ errorMessage }}
            </div>

            <Card class="overflow-hidden border-zinc-200/90 shadow-sm">
                <CardHeader class="border-b border-zinc-100 bg-zinc-50/80 pb-4">
                    <CardTitle class="text-lg text-zinc-900">Suscripciones</CardTitle>
                    <CardDescription class="text-zinc-600">
                        Datos sincronizados desde Partner Center. Las acciones respetan las reglas de Microsoft.
                    </CardDescription>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="hidden md:block">
                        <Table>
                            <TableHeader>
                                <TableRow class="border-zinc-200 hover:bg-transparent">
                                    <TableHead class="h-11 font-semibold text-zinc-700">Producto</TableHead>
                                    <TableHead class="h-11 w-28 font-semibold text-zinc-700">Cantidad</TableHead>
                                    <TableHead class="h-11 font-semibold text-zinc-700">Estado</TableHead>
                                    <TableHead class="h-11 text-right font-semibold text-zinc-700">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="license in licenses"
                                    :key="license.id"
                                    class="border-zinc-100 transition-colors hover:bg-zinc-50/80"
                                >
                                    <TableCell class="py-4 font-medium text-zinc-900">
                                        {{ license.offer_name }}
                                    </TableCell>
                                    <TableCell class="py-4 tabular-nums text-zinc-700">
                                        {{ license.quantity }}
                                    </TableCell>
                                    <TableCell class="py-4">
                                        <Badge variant="outline" :class="statusBadgeClass(license.status)">
                                            {{ license.status }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="py-4 text-right">
                                        <div class="flex flex-wrap justify-end gap-2">
                                            <Button
                                                size="sm"
                                                variant="outline"
                                                class="h-9 rounded-lg border-emerald-200 bg-white text-emerald-800 hover:bg-emerald-50 hover:text-emerald-900"
                                                @click="openIncrementModal(license)"
                                            >
                                                <ArrowUpCircle class="size-4" />
                                                Aumentar
                                            </Button>
                                            <Button
                                                size="sm"
                                                variant="outline"
                                                class="h-9 rounded-lg border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 disabled:opacity-40"
                                                :disabled="!canDecrease(license)"
                                                @click="openDecrementModal(license)"
                                            >
                                                <ArrowDownCircle class="size-4" />
                                                Reducir
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="!licenses.length" class="border-0 hover:bg-transparent">
                                    <TableCell colspan="4" class="py-16 text-center">
                                        <div class="mx-auto flex max-w-sm flex-col items-center gap-3">
                                            <div class="flex size-14 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-400">
                                                <PackagePlus class="size-7" />
                                            </div>
                                            <p class="text-sm font-medium text-zinc-800">No hay licencias aún</p>
                                            <p class="text-sm text-zinc-500">
                                                Pide al administrador que vincule tu cuenta con Microsoft o usa el botón de arriba para contactar a ReadyMind.
                                            </p>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <!-- Mobile cards -->
                    <div class="divide-y divide-zinc-100 md:hidden">
                        <div
                            v-for="license in licenses"
                            :key="license.id"
                            class="space-y-4 p-4"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-zinc-900">{{ license.offer_name }}</p>
                                    <p class="mt-1 text-sm text-zinc-500">
                                        Cantidad: <span class="font-medium text-zinc-800">{{ license.quantity }}</span>
                                    </p>
                                </div>
                                <Badge variant="outline" :class="statusBadgeClass(license.status)">
                                    {{ license.status }}
                                </Badge>
                            </div>
                            <div class="flex gap-2">
                                <Button
                                    size="sm"
                                    variant="outline"
                                    class="h-9 flex-1 rounded-lg border-emerald-200 text-emerald-800"
                                    @click="openIncrementModal(license)"
                                >
                                    <ArrowUpCircle class="size-4" />
                                    Aumentar
                                </Button>
                                <Button
                                    size="sm"
                                    variant="outline"
                                    class="h-9 flex-1 rounded-lg"
                                    :disabled="!canDecrease(license)"
                                    @click="openDecrementModal(license)"
                                >
                                    <ArrowDownCircle class="size-4" />
                                    Reducir
                                </Button>
                            </div>
                        </div>
                        <div v-if="!licenses.length" class="p-10 text-center text-sm text-zinc-500">
                            No hay licencias asignadas.
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Modal: incrementar -->
        <Dialog v-model:open="incrementModal">
            <DialogContent
                class="gap-0 overflow-hidden border-zinc-200/90 p-0 shadow-2xl sm:max-w-[26rem] rounded-2xl"
            >
                <div class="bg-gradient-to-br from-indigo-600 via-indigo-600 to-violet-600 px-6 py-5 text-white">
                    <div class="flex items-start gap-4 pr-8">
                        <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/20">
                            <ArrowUpCircle class="size-5 text-white" />
                        </div>
                        <DialogHeader class="space-y-1.5 text-left">
                            <DialogTitle class="text-lg font-semibold tracking-tight text-white">
                                Aumentar licencias
                            </DialogTitle>
                            <DialogDescription class="text-sm text-indigo-100">
                                {{ selectedSubscription?.offer_name }}
                            </DialogDescription>
                        </DialogHeader>
                    </div>
                </div>
                <div class="space-y-4 px-6 py-6">
                    <div>
                        <Label for="amount" class="text-zinc-700">Cantidad a añadir</Label>
                        <Input
                            id="amount"
                            v-model.number="incrementAmount"
                            type="number"
                            min="1"
                            class="mt-2 h-11 rounded-xl border-zinc-200"
                        />
                    </div>
                </div>
                <DialogFooter class="gap-2 border-t border-zinc-100 bg-zinc-50/90 px-6 py-4 sm:justify-end">
                    <Button variant="outline" class="h-10 rounded-xl" @click="incrementModal = false">
                        Cancelar
                    </Button>
                    <Button
                        class="h-10 rounded-xl bg-indigo-600 px-6 font-semibold text-white shadow-md shadow-indigo-600/20 hover:bg-indigo-700 hover:text-white focus-visible:text-white"
                        :disabled="submitting"
                        @click="submitIncrement"
                    >
                        <Loader2 v-if="submitting" class="size-4 animate-spin text-white" />
                        Confirmar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Modal: reducir -->
        <Dialog v-model:open="decrementModal">
            <DialogContent
                class="gap-0 overflow-hidden border-zinc-200/90 p-0 shadow-2xl sm:max-w-[26rem] rounded-2xl"
            >
                <div class="bg-gradient-to-br from-zinc-700 via-zinc-800 to-zinc-900 px-6 py-5 text-white">
                    <div class="flex items-start gap-4 pr-8">
                        <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/15">
                            <ArrowDownCircle class="size-5 text-white" />
                        </div>
                        <DialogHeader class="space-y-1.5 text-left">
                            <DialogTitle class="text-lg font-semibold tracking-tight text-white">
                                Reducir licencias
                            </DialogTitle>
                            <DialogDescription class="text-sm text-zinc-300">
                                {{ selectedSubscription?.offer_name }}
                            </DialogDescription>
                        </DialogHeader>
                    </div>
                </div>
                <div class="space-y-4 px-6 py-6">
                    <div>
                        <Label for="decrement_amount" class="text-zinc-700">Cantidad a reducir</Label>
                        <Input
                            id="decrement_amount"
                            v-model.number="decrementAmount"
                            type="number"
                            min="1"
                            class="mt-2 h-11 rounded-xl border-zinc-200"
                        />
                        <p class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs leading-relaxed text-amber-900">
                            Solo es posible durante la ventana de devolución definida por Microsoft para esta suscripción.
                        </p>
                    </div>
                </div>
                <DialogFooter class="gap-2 border-t border-zinc-100 bg-zinc-50/90 px-6 py-4 sm:justify-end">
                    <Button variant="outline" class="h-10 rounded-xl" @click="decrementModal = false">
                        Cancelar
                    </Button>
                    <Button
                        class="h-10 rounded-xl bg-zinc-900 px-6 font-semibold text-white shadow-md hover:bg-zinc-800 hover:text-white focus-visible:text-white"
                        :disabled="submitting"
                        @click="submitDecrement"
                    >
                        <Loader2 v-if="submitting" class="size-4 animate-spin text-white" />
                        Confirmar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Modal: solicitar productos -->
        <Dialog v-model:open="productRequestModal">
            <DialogContent
                class="gap-0 overflow-hidden border-zinc-200/90 p-0 shadow-2xl sm:max-w-lg rounded-2xl"
            >
                <div class="bg-gradient-to-br from-violet-600 to-indigo-600 px-6 py-5 text-white">
                    <div class="flex items-start gap-4 pr-8">
                        <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/20">
                            <Mail class="size-5 text-white" />
                        </div>
                        <DialogHeader class="space-y-1.5 text-left">
                            <DialogTitle class="text-lg font-semibold tracking-tight text-white">
                                Solicitud de productos
                            </DialogTitle>
                            <DialogDescription class="text-sm text-violet-100">
                                Cuéntanos qué necesitas. El equipo ReadyMind te contactará con los siguientes pasos.
                            </DialogDescription>
                        </DialogHeader>
                    </div>
                </div>
                <div class="grid gap-5 px-6 py-6">
                    <div>
                        <Label for="product_name" class="text-zinc-700">Producto deseado</Label>
                        <Input
                            id="product_name"
                            v-model="productName"
                            class="mt-2 h-11 rounded-xl border-zinc-200"
                            placeholder="Ej. Microsoft 365 Business Premium"
                        />
                    </div>
                    <div>
                        <Label for="requested_quantity" class="text-zinc-700">Cantidad aproximada</Label>
                        <Input
                            id="requested_quantity"
                            v-model.number="requestedQuantity"
                            type="number"
                            min="1"
                            class="mt-2 h-11 rounded-xl border-zinc-200"
                            placeholder="Ej. 25"
                        />
                    </div>
                    <div>
                        <Label for="description" class="text-zinc-700">Mensaje</Label>
                        <Textarea
                            id="description"
                            v-model="contactMessage"
                            rows="4"
                            placeholder="Plazos, uso previsto, integraciones…"
                            class="mt-2 min-h-[120px] rounded-xl border-zinc-200"
                        />
                    </div>
                </div>
                <DialogFooter class="gap-2 border-t border-zinc-100 bg-zinc-50/90 px-6 py-4 sm:justify-end">
                    <Button variant="outline" class="h-10 rounded-xl" @click="productRequestModal = false">
                        Cancelar
                    </Button>
                    <Button
                        class="h-10 rounded-xl bg-violet-600 px-6 font-semibold text-white shadow-md shadow-violet-600/25 hover:bg-violet-700 hover:text-white focus-visible:text-white"
                        :disabled="submitting"
                        @click="submitContactForm"
                    >
                        <Loader2 v-if="submitting" class="size-4 animate-spin text-white" />
                        Enviar solicitud
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </PortalLayout>
</template>
