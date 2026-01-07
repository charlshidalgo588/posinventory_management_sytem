<template>
  <div class="h-screen flex flex-col items-center justify-center bg-gray-100 text-center">
    <h1 class="text-6xl font-bold text-red-600 mb-4">404</h1>
    <p class="text-gray-600 text-xl mb-8">Oops! Page not found.</p>
    <RouterLink to="/" class="text-blue-500 underline">Go back home</RouterLink>
  </div>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>
