<script setup lang="ts">
import { cn } from '@/lib/utils';
</script>

<template>
    <thead :class="cn('[&_tr]:border-b')">
        <slot />
    </thead>
</template>
