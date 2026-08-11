<script setup>
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { searchUsers } from '@/api/search'
import { useAuthStore } from '@/stores/auth'
import Avatar from '@/components/Avatar.vue'

const router = useRouter()
const auth = useAuthStore()

const query = ref('')
const results = ref([])
const showDropdown = ref(false)
const loading = ref(false)
let debounceTimer = null

async function runSearch() {
  if (!query.value.trim()) {
    results.value = []
    return
  }
  loading.value = true
  const { data } = await searchUsers(query.value)
  results.value = data.data
  loading.value = false
}

watch(query, () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(runSearch, 400)
})

function clearSearch() {
  query.value = ''
  results.value = []
}

function selectUser(username) {
  clearSearch()
  showDropdown.value = false
  router.push({ name: 'user-profile', params: { username } })
}

async function handleLogout() {
  await auth.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <nav class="navbar">
    <router-link :to="{ name: 'home' }" class="logo">Muse</router-link>

    <div class="search-wrapper">
      <div class="search-input-box">
        <svg class="icon-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input
          v-model="query"
          type="text"
          placeholder="Buscar pessoas no Muse"
          @focus="showDropdown = true"
          @blur="showDropdown = false"
        />
        <button v-if="query" class="clear-btn" @mousedown.prevent="clearSearch">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      <div v-if="showDropdown && query.trim()" class="search-dropdown">
        <div v-if="loading" class="dropdown-msg">Buscando...</div>
        <div v-else-if="results.length === 0" class="dropdown-msg">Nenhum usuário encontrado.</div>

        <button
          v-for="user in results"
          :key="user.id"
          class="dropdown-item"
          @mousedown.prevent="selectUser(user.username)"
        >
          <Avatar :name="user.name" :avatar-path="user.avatar_path" :size="36" />
          <div class="dropdown-user-info">
            <strong>{{ user.name }}</strong>
            <p>@{{ user.username }}</p>
          </div>
        </button>
      </div>
    </div>

    <div class="nav-right">
      <router-link :to="{ name: 'profile' }">
        <Avatar :name="auth.user?.name || ''" :avatar-path="auth.user?.avatar_path" :size="38" />
      </router-link>
      <button class="logout-btn" @click="handleLogout" title="Sair">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      </button>
    </div>
  </nav>
</template>

<style scoped>
.navbar {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  gap: 1rem;
  padding: 1rem 2rem;
  background: var(--color-surface);
  border-bottom: 1px solid var(--color-border);
}
.logo {
  font-family: 'Georgia', serif;
  font-size: 1.6rem;
  font-weight: 700;
  color: var(--color-primary);
  text-decoration: none;
  justify-self: start;
}
.search-wrapper {
  position: relative;
  justify-self: center;
  width: 32rem;
  max-width: 70vw;
}
.search-input-box {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: var(--color-background);
  border: 1px solid transparent;
  border-radius: 999px;
  padding: 0.55rem 1rem;
}
.search-input-box:focus-within {
  border-color: var(--color-primary);
  background: var(--color-surface);
  box-shadow: 0 0 0 4px color-mix(in oklab, var(--color-primary) 16%, transparent);
}
.icon-search {
  width: 18px;
  height: 18px;
  color: var(--color-text-muted);
  flex-shrink: 0;
}
.search-input-box input {
  flex: 1;
  border: none;
  background: none;
  outline: none;
  font-family: inherit;
  font-size: 0.9rem;
  color: var(--color-text);
}
.search-input-box input::placeholder {
  color: var(--color-text-muted);
}
.clear-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--color-text-muted);
  display: flex;
}
.clear-btn svg {
  width: 16px;
  height: 16px;
}
.search-dropdown {
  position: absolute;
  top: calc(100% + 0.6rem);
  left: 0;
  width: 100%;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-card);
  max-height: 320px;
  overflow-y: auto;
  z-index: 20;
  padding: 0.4rem;
}
.dropdown-msg {
  padding: 0.75rem;
  color: var(--color-text-muted);
  font-size: 0.85rem;
}
.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  width: 100%;
  padding: 0.55rem 0.6rem;
  background: none;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  text-align: left;
  font-family: inherit;
}
.dropdown-item:hover {
  background: var(--color-background);
}
.dropdown-user-info strong {
  display: block;
  font-size: 0.88rem;
}
.dropdown-user-info p {
  font-size: 0.78rem;
  color: var(--color-text-muted);
  margin: 0;
}
.nav-right {
  display: flex;
  align-items: center;
  gap: 0.9rem;
  justify-self: end;
}
.logout-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--color-text-muted);
  display: flex;
}
.logout-btn svg {
  width: 20px;
  height: 20px;
}
@media (max-width: 640px) {
  .navbar {
    grid-template-columns: 1fr 1fr;
    grid-template-areas: "logo right" "search search";
    padding: 0.85rem 1rem;
    row-gap: 0.6rem;
  }
  .logo { grid-area: logo; }
  .nav-right { grid-area: right; }
  .search-wrapper { grid-area: search; width: 100%; max-width: 100%; }
}
</style>