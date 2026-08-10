<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

const form = ref({ email: '', password: '' })
const errors = ref({})
const loading = ref(false)

async function handleSubmit() {
  errors.value = {}
  loading.value = true

  try {
    await auth.login(form.value)
    router.push({ name: 'home' })
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
    } else {
      errors.value = { general: ['Erro ao fazer login. Tenta de novo.'] }
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="auth-page">
    <form @submit.prevent="handleSubmit">
      <h1>Entrar</h1>

      <div v-if="errors.general" class="error">{{ errors.general[0] }}</div>

      <label>
        Email
        <input v-model="form.email" type="email" required />
        <span v-if="errors.email" class="error">{{ errors.email[0] }}</span>
      </label>

      <label>
        Senha
        <input v-model="form.password" type="password" required />
        <span v-if="errors.password" class="error">{{ errors.password[0] }}</span>
      </label>

      <button type="submit" :disabled="loading">
        {{ loading ? 'Entrando...' : 'Entrar' }}
      </button>

      <p>
        Não tem conta?
        <router-link :to="{ name: 'register' }">Registrar</router-link>
      </p>
    </form>
  </div>
</template>

<style scoped>
.auth-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
}
form {
  width: 320px;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}
label {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  font-size: 0.9rem;
}
input {
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}
button {
  padding: 0.6rem;
  border: none;
  border-radius: 4px;
  background: #0095f6;
  color: white;
  cursor: pointer;
}
button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.error {
  color: #e0245e;
  font-size: 0.8rem;
}
</style>