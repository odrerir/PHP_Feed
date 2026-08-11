<script setup>
import { ref, onMounted } from 'vue'
import { fetchFeed, createPost } from '@/api/posts'
import { searchUsers } from '@/api/search'
import { useAuthStore } from '@/stores/auth'
import PostCard from '@/components/PostCard.vue'
import Avatar from '@/components/Avatar.vue'

const auth = useAuthStore()

const posts = ref([])
const page = ref(1)
const lastPage = ref(1)
const loading = ref(true)
const loadingMore = ref(false)

const suggestions = ref([])

const showForm = ref(false)
const caption = ref('')
const mediaFile = ref(null)
const posting = ref(false)
const errors = ref({})

async function loadFeed(pageNumber = 1) {
  const { data } = await fetchFeed(pageNumber)
  posts.value = pageNumber === 1 ? data.data : [...posts.value, ...data.data]
  page.value = data.current_page
  lastPage.value = data.last_page
}

async function loadSuggestions() {
  // reaproveita o endpoint de busca sem termo nenhum, que já devolve
  // todos os usuários exceto você mesmo — pega só os 4 primeiros
  const { data } = await searchUsers('')
  suggestions.value = data.data.slice(0, 4)
}

async function loadMore() {
  loadingMore.value = true
  await loadFeed(page.value + 1)
  loadingMore.value = false
}

function handleFileChange(event) {
  mediaFile.value = event.target.files[0] || null
}

async function handleCreatePost() {
  errors.value = {}
  posting.value = true

  const formData = new FormData()
  formData.append('caption', caption.value)
  if (mediaFile.value) formData.append('media', mediaFile.value)

  try {
    await createPost(formData)
    caption.value = ''
    mediaFile.value = null
    showForm.value = false
    await loadFeed(1)
  } catch (err) {
    if (err.response?.status === 422) errors.value = err.response.data.errors || {}
  } finally {
    posting.value = false
  }
}

onMounted(async () => {
  loading.value = true
  await Promise.all([loadFeed(1), loadSuggestions()])
  loading.value = false
})
</script>

<template>
  <div class="home-layout">
    <div class="feed-column">
      <div class="composer-card">
        <template v-if="!showForm">
          <Avatar :name="auth.user?.name || ''" :avatar-path="auth.user?.avatar_path" :size="40" />
          <button class="composer-prompt" @click="showForm = true">
            Compartilhe com seus amigos {{ auth.user?.name?.split(' ')[0] || '' }}
          </button>
          <button class="new-post-btn" @click="showForm = true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
            Novo post
          </button>
        </template>

        <form v-else @submit.prevent="handleCreatePost" class="new-post-form">
          <textarea v-model="caption" placeholder="Legenda (opcional)" rows="2" />
          <input type="file" accept="image/*" required @change="handleFileChange" />
          <span v-if="errors.media" class="error">{{ errors.media[0] }}</span>
          <div class="form-actions">
            <button type="submit" :disabled="posting">{{ posting ? 'Publicando...' : 'Publicar' }}</button>
            <button type="button" class="cancel-btn" @click="showForm = false">Cancelar</button>
          </div>
        </form>
      </div>

      <div v-if="loading">Carregando feed...</div>

      <div v-else>
        <p v-if="posts.length === 0" class="empty-msg">Nenhum post ainda. Siga alguém ou crie o primeiro!</p>
        <PostCard v-for="post in posts" :key="post.id" :post="post" />
        <button v-if="page < lastPage" class="load-more-btn" @click="loadMore" :disabled="loadingMore">
          {{ loadingMore ? 'Carregando...' : 'Carregar mais' }}
        </button>
      </div>
    </div>

    <aside class="sidebar">
      <div class="suggestions-card">
        <h2>Talvez você goste</h2>
        <div
          v-for="user in suggestions"
          :key="user.id"
          class="suggestion-row"
        >
          <Avatar :name="user.name" :avatar-path="user.avatar_path" :size="38" />
          <div class="suggestion-info">
            <router-link :to="{ name: 'user-profile', params: { username: user.username } }" class="name-link">
              <strong>{{ user.name }}</strong>
            </router-link>
            <span>@{{ user.username }}</span>
          </div>
          <router-link :to="{ name: 'user-profile', params: { username: user.username } }" class="ver-link">
            <span>ver</span>
          </router-link>
        </div>
      </div>
    </aside>
  </div>
</template>

<style scoped>
.home-layout {
  display: grid;
  grid-template-columns: 1fr 320px;
  gap: 2rem;
  align-items: start;
}
.feed-column { min-width: 0; }
.composer-card {
  display: flex;
  align-items: center;
  gap: 0.9rem;
  padding: 1rem 1.2rem;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-card);
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}
.composer-prompt {
  flex: 1;
  text-align: left;
  background: var(--color-background);
  border: none;
  border-radius: 999px;
  padding: 0.7rem 1rem;
  font-family: inherit;
  font-size: 0.88rem;
  color: var(--color-text-muted);
  cursor: pointer;
  min-width: 160px;
}
.new-post-btn {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.65rem 1.1rem;
  font-weight: 600;
  font-family: inherit;
  font-size: 0.88rem;
  cursor: pointer;
}
.new-post-btn:hover { background: var(--color-primary-hover); }
.new-post-btn svg { width: 18px; height: 18px; }
.new-post-form { display: flex; flex-direction: column; gap: 0.6rem; width: 100%; }
.new-post-form textarea,
.new-post-form input[type='file'] {
  font-family: inherit;
  padding: 0.6rem;
  border: 1px solid var(--color-border);
  border-radius: 8px;
  background: var(--color-background);
}
.form-actions { display: flex; gap: 0.6rem; }
.form-actions button[type='submit'] {
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.6rem 1.2rem;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
}
.cancel-btn {
  background: none;
  border: 1px solid var(--color-border);
  border-radius: 8px;
  padding: 0.6rem 1.2rem;
  cursor: pointer;
  font-family: inherit;
  color: var(--color-text);
}
.empty-msg { color: var(--color-text-muted); text-align: center; padding: 2rem 0; }
.load-more-btn {
  display: block;
  width: 100%;
  padding: 0.7rem;
  border: 1px solid var(--color-border);
  border-radius: 8px;
  background: var(--color-surface);
  cursor: pointer;
  font-family: inherit;
  color: var(--color-text);
}
.sidebar { position: sticky; top: 1rem; }
.suggestions-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-card);
  padding: 1.2rem;
}
.suggestions-card h2 { font-size: 0.95rem; margin: 0 0 1rem; }
.suggestion-row {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  padding: 0.55rem 0;
  text-decoration: none;
  color: inherit;
}
.suggestion-info { 
  flex: 1; 
  min-width: 
  0; 
}
.suggestion-info strong { 
  display: block; 
  font-size: 
  0.85rem; 
}
.suggestion-info span { 
  font-size: 0.76rem; 
  color: var(--color-text-muted); 
}
.name-link {  
  font-size: 14px;
  color: var(--color-text);
  text-decoration: none;
  width: fit-content;
  transition: color 0.15s;
}
.name-link:hover {
  color: var(--color-primary);
}
.ver-link { 
  font-size: 0.9rem; 
  text-decoration: none;
  color: var(--color-primary); 
  font-weight: 600; 
}
.error { 
  color: var(--color-error); 
  font-size: 0.8rem; 
}
@media (max-width: 860px) {
  .home-layout { grid-template-columns: 1fr; }
  .sidebar { display: none; }
}
</style>