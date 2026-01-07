<template>
  <router-link
    :to="to"
    class="flex items-center p-2 rounded-lg text-white hover:bg-orange-300 transition"
    :class="{ 'bg-orange-500 text-white': active }"
  >
    <i :class="['mr-3', icon]"></i>
    <span>{{ label }}</span>
  </router-link>
</template>

<script>
export default {
  props: {
    to: String,
    label: String,
    icon: String,
    active: Boolean,
    menuItem: Boolean,
  },
}
</script>
