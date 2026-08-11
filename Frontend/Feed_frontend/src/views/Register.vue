<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

const form = ref({
  name: '',
  username: '',
  email: '',
  password: '',
  password_confirmation: '',
})
const errors = ref({})
const loading = ref(false)

async function handleSubmit() {
  errors.value = {}
  loading.value = true

  try {
    await auth.register(form.value)
    router.push({ name: 'home' })
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
    } else {
      errors.value = { general: ['Erro ao registrar. Tenta de novo.'] }
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="auth-page">
    <h1 class="logo">Muse</h1>
    <p class="tagline">Um cantinho tranquilo para suas fotos.</p>

    <div class="auth-card">
      <form @submit.prevent="handleSubmit">
        <div v-if="errors.general" class="error general-error">{{ errors.general[0] }}</div>

        <label>
          Nome
          <input v-model="form.name" type="text" placeholder="Como te chamar?" required />
          <span v-if="errors.name" class="error">{{ errors.name[0] }}</span>
        </label>

        <label>
          Usuário
          <input v-model="form.username" type="text" placeholder="@seuusuario" required />
          <span v-if="errors.username" class="error">{{ errors.username[0] }}</span>
        </label>

        <label>
          E-mail
          <input v-model="form.email" type="email" placeholder="exemplo@email.com" required />
          <span v-if="errors.email" class="error">{{ errors.email[0] }}</span>
        </label>

        <label>
          Senha
          <input v-model="form.password" type="password" placeholder="••••••••" required />
          <span class="hint">Use pelo menos 8 caracteres.</span>
          <span v-if="errors.password" class="error">{{ errors.password[0] }}</span>
        </label>

        <label>
          Confirmar senha
          <input v-model="form.password_confirmation" type="password" placeholder="••••••••" required />
        </label>

        <button type="submit" :disabled="loading">
          {{ loading ? 'Criando...' : 'Criar minha conta' }}
        </button>
      </form>
    </div>

    <p class="switch-auth">
      Já tem conta?
      <router-link :to="{ name: 'login' }">Entrar</router-link>
    </p>
  </div>
</template>

<style scoped>
.auth-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
}
.logo {
  font-family: 'Georgia', serif;
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--color-primary);
  margin: 0 0 0.25rem;
}
.tagline {
  color: var(--color-text-muted);
  margin: 0 0 2rem;
  font-size: 0.95rem;
}
.auth-card {
  width: 380px;
  max-width: 90vw;
  padding: 2rem;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-card);
}
form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
label {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  font-size: 0.9rem;
  font-weight: 500;
}
input {
  padding: 0.75rem 0.9rem;
  border: 1px solid var(--color-border);
  border-radius: 8px;
  background: var(--color-surface);
  font-size: 0.9rem;
  font-family: inherit;
  color: var(--color-text);
}
input::placeholder {
  color: var(--color-text-muted);
}
input:focus {
  outline: none;
  border-color: var(--color-primary);
}
.hint {
  font-size: 0.78rem;
  color: var(--color-text-muted);
}
button {
  margin-top: 0.5rem;
  padding: 0.85rem;
  border: none;
  border-radius: 8px;
  background: var(--color-primary);
  color: white;
  font-weight: 600;
  font-family: inherit;
  font-size: 0.95rem;
  cursor: pointer;
  transition: background 0.15s;
}
button:hover:not(:disabled) {
  background: var(--color-primary-hover);
}
button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.switch-auth {
  margin-top: 1.5rem;
  font-size: 0.9rem;
  color: var(--color-text-muted);
}
.switch-auth a {
  color: var(--color-primary);
  font-weight: 600;
  text-decoration: none;
}
.error {
  color: var(--color-error);
  font-size: 0.8rem;
}
.general-error {
  text-align: center;
}
</style>