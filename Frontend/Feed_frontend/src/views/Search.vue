<script setup>
import { ref, watch } from 'vue'
import { searchUsers } from '@/api/search'
import { STORAGE_URL } from '@/api/axios'

const query = ref('')
const results = ref([])
const loading = ref(false)
let debounceTimer = null

function avatarUrl(path) {
  return path ? `${STORAGE_URL}/${path}` : null
}

async function runSearch() {
  loading.value = true
  const { data } = await searchUsers(query.value)
  results.value = data.data
  loading.value = false
}

watch(query, () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(runSearch, 400)
})

// carrega a lista geral assim que a tela abre, sem esperar o usuário digitar nada
runSearch()
</script>

<template>
  <div>
    <input
      v-model="query"
      type="text"
      placeholder="Buscar por nome ou username..."
      class="search-input"
    />

    <div v-if="loading">Buscando...</div>

    <div v-else>
      <p v-if="results.length === 0">Nenhum usuário encontrado.</p>

      <router-link
        v-for="user in results"
        :key="user.id"
        :to="{ name: 'user-profile', params: { username: user.username } }"
        class="result-row"
      >
        <img v-if="avatarUrl(user.avatar_path)" :src="avatarUrl(user.avatar_path)" class="avatar" />
        <div v-else class="avatar avatar-placeholder" />

        <div>
          <strong>@{{ user.username }}</strong>
          <p>{{ user.name }}</p>
        </div>
      </router-link>
    </div>
  </div>
</template>

<style scoped>
.search-input {
  width: 100%;
  padding: 0.6rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  margin-bottom: 1rem;
}
.result-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.6rem 0;
  text-decoration: none;
  color: inherit;
  border-bottom: 1px solid #efefef;
}
.avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  object-fit: cover;
}
.avatar-placeholder {
  background: #dbdbdb;
}
.result-row p {
  font-size: 0.85rem;
  color: #8e8e8e;
  margin: 0;
}
</style>