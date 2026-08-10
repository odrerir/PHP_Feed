<script setup>
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { searchUsers } from '@/api/search'
import { STORAGE_URL } from '@/api/axios'
import { useAuthStore } from '@/stores/auth'
import DefaultAvatar from '@/assets/profile/Avatar.svg'

const router = useRouter()
const auth = useAuthStore()

const query = ref('')
const results = ref([])
const showDropdown = ref(false)
const loading = ref(false)
let debounceTimer = null

function avatarUrl(path) {
  return path ? `${STORAGE_URL}/${path}` : DefaultAvatar
}

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

function selectUser(username) {
  query.value = ''
  results.value = []
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
    <div class="nav-block">
      <router-link :to="{ name: 'home' }" class="nav-link">Home</router-link>
      <router-link :to="{ name: 'profile' }" class="nav-link">Profile</router-link>
    </div>
    <div class="nav-block" id="search-bar">
      <div class="search-wrapper">
        <input
          v-model="query"
          type="text"
          placeholder="Pesquisar..."
          class="search-input"
          @focus="showDropdown = true"
          @blur="showDropdown = false"
        />

        <div v-if="showDropdown && query.trim()" class="search-dropdown">
          <div v-if="loading" class="dropdown-msg">Buscando...</div>
          <div v-else-if="results.length === 0" class="dropdown-msg">Nenhum usuário encontrado.</div>

          <button
            v-for="user in results"
            :key="user.id"
            class="dropdown-item"
            @mousedown.prevent="selectUser(user.username)"
          >
            <img :src="avatarUrl(user.avatar_path)" class="avatar" />
            <div class="dropdown-user-info">
              <strong>@{{ user.username }}</strong>
              <p>{{ user.name }}</p>
            </div>
          </button>
        </div>
      </div>
    </div>

    <div class="nav-block">
      <button @click="handleLogout" class="nav-link logout-btn">Sair</button>
    </div>
  </nav>
</template>


<style scoped>
.navbar {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  grid-template-areas: "links search logout";
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #dbdbdb;
  gap: 0.75rem;
}
.nav-block:first-child {
  grid-area: links;
  justify-self: start;
}
#search-bar {
  grid-area: search;
  justify-self: center;
}
.nav-block:last-child {
  grid-area: logout;
  justify-self: end;
}
.nav-block {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}
.nav-link {
  text-decoration: none;
  color: #262626;
  font-weight: 500;
  white-space: nowrap;
}
.router-link-exact-active {
  font-weight: 700;
  color: #0095f6;
}
.search-wrapper {
  position: relative;
  width: 40rem;
  max-width: 70vw;
  padding: 0.2rem 0.4rem;
}
.search-input {
  width: 100%;
  padding: 0.4rem 0.6rem;
  border: 1px solid #dbdbdb;
  border-radius: 6px;
  box-sizing: border-box;
}
.search-dropdown {
  position: absolute;
  top: calc(100% + 0.5rem);
  left: 0;
  width: 100%;
  min-width: 280px;
  background: white;
  border: 1px solid #dbdbdb;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  max-height: 320px;
  overflow-y: auto;
  z-index: 10;
}
.dropdown-msg {
  padding: 0.75rem;
  color: #8e8e8e;
  font-size: 0.9rem;
}
.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  width: 100%;
  padding: 0.5rem 0.75rem;
  background: none;
  border: none;
  cursor: pointer;
  text-align: left;
}
.dropdown-item:hover {
  background: #fafafa;
}
.avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}
.avatar-placeholder {
  background: #dbdbdb;
}
.dropdown-user-info p {
  font-size: 0.8rem;
  color: #8e8e8e;
  margin: 0;
}
.logout-btn {
  margin-left: auto;
  background: none;
  border: none;
  cursor: pointer;
  font-family: inherit;
  font-size: inherit;
}

/* Tablet: busca ainda cabe na mesma linha, só encolhe um pouco */
@media (max-width: 900px) {
  .search-wrapper {
    width: 100%;
    max-width: 50vw;
  }
}

/* Mobile: empilha em 2 linhas — links+logout em cima, busca ocupando a largura toda embaixo */
@media (max-width: 640px) {
  .navbar {
    grid-template-columns: 1fr 1fr;
    grid-template-areas:
      "links logout"
      "search search";
    row-gap: 0.6rem;
    padding: 0.75rem;
  }
  .nav-block {
    gap: 1rem;
  }
  .search-wrapper {
    width: 20rem;
    max-width: 100%;
    padding: 0;
  }
}

/* Telas bem pequenas: aperta ainda mais o espaçamento e a fonte */
@media (max-width: 380px) {
  .nav-block {
    gap: 0.6rem;
  }
  .nav-link {
    font-size: 0.9rem;
  }
  .search-wrapper {
    width: 100%;
    max-width: 100%;
    padding: 0;
  }
}
</style>