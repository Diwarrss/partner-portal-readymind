<script setup lang="ts">
import type { TabsTriggerProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { reactiveOmit } from "@vueuse/core"
import { TabsTrigger, useForwardProps } from "reka-ui"
import { cn } from "@/lib/utils"

const props = defineProps<TabsTriggerProps & { class?: HTMLAttributes["class"] }>()

const delegatedProps = reactiveOmit(props, "class")

const forwardedProps = useForwardProps(delegatedProps)
</script>

<template>
  <TabsTrigger
    v-bind="forwardedProps"
    :class="cn(
      'flex items-center justify-center rounded-lg px-3 py-2 text-sm font-medium gap-x-2 whitespace-nowrap transition-all duration-75 outline-none',
      'text-gray-500',
      'hover:bg-gray-50',
      'focus:bg-gray-50',
      'data-[state=active]:bg-gray-50',
      'data-[state=active]:text-primary-600',
      'disabled:pointer-events-none disabled:opacity-50',
      props.class,
    )"
  >
    <slot />
  </TabsTrigger>
</template>
