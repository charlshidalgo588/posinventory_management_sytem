<template>
  <li>
    <button
      @click="$emit('toggle')"
      class="flex items-center w-full p-2 rounded-lg text-white hover:bg-orange-300"
    >
      <i :class="['mr-3', icon]"></i>
      <span class="flex-1 text-left">{{ label }}</span>
      <i :class="['fa-solid fa-chevron-down transition-transform', { 'rotate-180': open }]"></i>
    </button>

    <ul v-if="open" class="py-2 space-y-2">
      <slot />
    </ul>
  </li>
</template>

<script>
export default {
  props: {
    label: String,
    icon: String,
    open: Boolean,
  },
}
</script>
