<script setup>
import { ref, onMounted } from 'vue'
import { fetchHome, createPost } from '@/api/posts'
import { useAuthStore } from '@/stores/auth'
import PostCard from '@/components/PostCard.vue'
import Avatar from '@/components/Avatar.vue'
import Spinner from '@/components/Spinner.vue'

const auth = useAuthStore()

const posts = ref([])
const page = ref(1)
const lastPage = ref(1)
const loading = ref(true)
const loadingMore = ref(false)
const fileInput = ref(null)

const suggestions = ref([])

const showForm = ref(false)
const caption = ref('')
const mediaFile = ref(null)
const posting = ref(false)
const errors = ref({})

async function loadHome(pageNumber = 1) {
  const { data } = await fetchHome(pageNumber)
  posts.value = pageNumber === 1 ? data.feed.data : [...posts.value, ...data.feed.data]
  page.value = data.feed.current_page
  lastPage.value = data.feed.last_page
  if (pageNumber === 1) suggestions.value = data.suggestions
}

async function loadMore() {
  loadingMore.value = true
  await loadHome(page.value + 1)
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
    await loadHome(1)
  } catch (err) {
    if (err.response?.status === 422) errors.value = err.response.data.errors || {}
  } finally {
    posting.value = false
  }
}

onMounted(async () => {
  loading.value = true
  await loadHome(1)
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
            <i class="bi bi-image"></i>
            Novo post
          </button>
        </template>

        <form v-else @submit.prevent="handleCreatePost" class="new-post-form">
          <textarea v-model="caption" placeholder="Legenda (opcional)" rows="2" />
          <div class="file-picker">
            <label for="media-upload" class="file-upload-btn">
              <i class="bi bi-image"></i>
              {{ mediaFile ? 'Trocar imagem' : 'Escolher imagem' }}
            </label>
            <span v-if="mediaFile" class="file-name">{{ mediaFile.name }}</span>
            <input id="media-upload" type="file" accept="image/*" class="hidden-input" @change="handleFileChange" />
          </div>
          <span v-if="errors.media" class="error">{{ errors.media[0] }}</span>
          <div class="form-actions">
            <button type="submit" :disabled="posting">{{ posting ? 'Publicando...' : 'Publicar' }}</button>
            <button type="button" class="cancel-btn" @click="showForm = false">Cancelar</button>
          </div>
        </form>
      </div>

      <Spinner v-if="loading" />

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
.new-post-btn:hover { 
  background: var(--color-primary-hover); 
}

.new-post-btn svg { 
  width: 18px; 
  height: 18px; 
}

.new-post-form {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  width: 100%;
}

.new-post-form textarea {
  font-family: inherit;
  padding: 0.6rem;
  border: 1px solid var(--color-border);
  border-radius: 8px;
  background: var(--color-background);
  min-height: 120px;
  max-height: 220px;
  resize: none;
}

.file-upload-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.6rem 1.2rem;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  text-align: center;
}

.file-input-hidden {
  display: none;
}

.hidden-input { 
  display: none; 
}

.file-picker {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.file-picker i{
  margin-right: 0.4rem;
}

.file-btn {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  background: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: 8px;
  padding: 0.55rem 0.9rem;
  font-family: inherit;
  font-size: 0.85rem;
  color: var(--color-text);
  cursor: pointer;
}

.file-name {
  font-size: 0.82rem;
  color: var(--color-text-muted);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 180px;
}

.form-actions {
  display: flex;
  gap: 0.6rem;
}

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