<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();
</script>

<template>
    <Head title="Profile" />

    <AdminLayout>
        <div class="space-y-6">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-zinc-900">
                    Profile
                </h2>
                <p class="mt-1 text-sm text-zinc-500">
                    Manage your account settings.
                </p>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        update-route="admin.profile.update"
                    />
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <UpdatePasswordForm />
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <DeleteUserForm destroy-route="admin.profile.destroy" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
