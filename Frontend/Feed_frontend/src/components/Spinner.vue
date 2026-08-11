<script setup>
defineProps({ size: { type: Number, default: 32 } })
</script>

<template>
  <div class="spinner-wrapper">
    <div class="spinner" :style="{ width: size + 'px', height: size + 'px' }"></div>
  </div>
</template>

<style scoped>
.spinner-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 0;
}
.spinner {
  border: 3px solid var(--color-border);
  border-top-color: var(--color-primary);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>