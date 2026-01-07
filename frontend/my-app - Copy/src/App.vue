<template>
  <!-- Render the current page -->
  <RouterView />
</template>

<script setup>
// no global layout here — prevents affecting all pages
</script>

<style>
html,
body {
  margin: 0;
  padding: 0;
  width: 100%;
  height: auto; /* allow scrolling */
  overflow: auto; /* FIX: let pages scroll */
}

#app {
  width: 100%;
  height: auto;
}
</style>
