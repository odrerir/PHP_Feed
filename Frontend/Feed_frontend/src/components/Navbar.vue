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
        <i class="bi bi-search icon-search"></i>
        <input
          v-model="query"
          type="text"
          placeholder="Buscar pessoas no Muse"
          @focus="showDropdown = true"
          @blur="showDropdown = false"
        />
        <button v-if="query" class="clear-btn" @mousedown.prevent="clearSearch">
          <i class="bi bi-x-lg"></i> 
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
          <Avatar :name="user.name" :avatar-path="user.avatar_path" :size="40" />
          <div class="dropdown-user-info">
            <strong>{{ user.name }}</strong>
            <p>@{{ user.username }}</p>
          </div>
        </button>
      </div>
    </div>

    <div class="nav-right">
      <router-link :to="{ name: 'profile' }">
        <Avatar :name="auth.user?.name || ''" :avatar-path="auth.user?.avatar_path" :size="60" />
      </router-link>
      <button class="logout-btn" @click="handleLogout" title="Sair">
        <i class="bi bi-box-arrow-right"></i>
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
.clear-btn {
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
  padding: 0.4rem;
  border: none;
  cursor: pointer;
  color: var(--color-text-muted);
  display: flex;
  transition: color 0.15s;
}
.logout-btn:hover {
  color: var(--color-error);
  border-radius: 8px;
  background-color: var(--color-border);
}
.clear-btn i, .logout-btn i { 
  font-size: 1.3rem; 
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