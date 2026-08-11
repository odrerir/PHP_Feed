<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

function goHome() {
  router.push({ name: 'home' })
}
</script>

<template>
  <div class="not-found">
    <p class="code">404</p>
    <i class="bi bi-flower1 icon"></i>
    <h1>Essa página murchou</h1>
    <p class="subtitle">A página que você procura não existe ou foi movida.</p>
    <button @click="goHome">
      <i class="bi bi-house"></i>
      Voltar para o início
    </button>
  </div>
</template>

<style scoped>
.not-found {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 1.5rem;
}
.code {
  font-family: 'Georgia', serif;
  font-size: 5rem;
  font-weight: 700;
  color: var(--color-primary);
  margin: 0;
  line-height: 1;
}
.icon {
  font-size: 2.2rem;
  color: var(--color-accent);
  margin: 0.75rem 0 1.25rem;
}
h1 {
  font-size: 1.3rem;
  margin: 0 0 0.5rem;
  color: var(--color-text);
}
.subtitle {
  color: var(--color-text-muted);
  font-size: 0.9rem;
  margin: 0 0 2rem;
}
button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.4rem;
  border: none;
  border-radius: 8px;
  background: var(--color-primary);
  color: white;
  font-weight: 600;
  font-family: inherit;
  font-size: 0.9rem;
  cursor: pointer;
  transition: background 0.15s;
}
button:hover {
  background: var(--color-primary-hover);
}
button i {
  font-size: 1rem;
}
</style>