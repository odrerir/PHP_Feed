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
  max-width: 1080px;
  margin: 0 auto;
  padding: 2rem 1.5rem;
}
</style>