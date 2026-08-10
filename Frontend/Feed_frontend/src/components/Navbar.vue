<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

async function handleLogout() {
  await auth.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <nav class="navbar">
    <router-link :to="{ name: 'home' }" class="nav-link">Home</router-link>
    <router-link :to="{ name: 'search' }" class="nav-link">Search</router-link>
    <router-link :to="{ name: 'profile' }" class="nav-link">Profile</router-link>
    <button @click="handleLogout" class="nav-link logout-btn">Sair</button>
  </nav>
</template>

<style scoped>
.navbar {
  display: flex;
  gap: 1.5rem;
  padding: 1rem;
  border-bottom: 1px solid #dbdbdb;
}
.nav-link {
  text-decoration: none;
  color: #262626;
  font-weight: 500;
}
.router-link-exact-active {
  font-weight: 700;
  color: #0095f6;
}
.logout-btn {
  margin-left: auto;
  background: none;
  border: none;
  cursor: pointer;
  font-family: inherit;
  font-size: inherit;
}
</style>