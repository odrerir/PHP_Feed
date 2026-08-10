<script setup>
import Navbar from '@/components/Navbar.vue'
</script>

<template>
  <div class="app-layout">
    <Navbar />
    <main class="content">
      <router-view />
    </main>
  </div>
</template>

<style scoped>
.content {
  max-width: 640px;
  margin: 0 auto;
  padding: 1.5rem 1rem;
}
</style>