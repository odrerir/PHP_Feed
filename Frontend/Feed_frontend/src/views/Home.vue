<script setup>
import { ref, onMounted } from 'vue'
import { fetchFeed, createPost } from '@/api/posts'
import PostCard from '@/components/PostCards.vue'

const posts = ref([])
const page = ref(1)
const lastPage = ref(1)
const loading = ref(true)
const loadingMore = ref(false)

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
  await loadFeed(1)
  loading.value = false
})
</script>

<template>
  <div>
    <button v-if="!showForm" @click="showForm = true" class="new-post-btn">+ Novo post</button>

    <form v-else @submit.prevent="handleCreatePost" class="new-post-form">
      <textarea v-model="caption" placeholder="Legenda (opcional)" rows="2" />
      <input type="file" accept="image/*" required @change="handleFileChange" />
      <span v-if="errors.media" class="error">{{ errors.media[0] }}</span>
      <div class="form-actions">
        <button type="submit" :disabled="posting">{{ posting ? 'Publicando...' : 'Publicar' }}</button>
        <button type="button" @click="showForm = false">Cancelar</button>
      </div>
    </form>

    <div v-if="loading">Carregando feed...</div>

    <div v-else>
      <p v-if="posts.length === 0">Nenhum post ainda. Siga alguém ou crie o primeiro!</p>
      <PostCard v-for="post in posts" :key="post.id" :post="post" />
      <button v-if="page < lastPage" @click="loadMore" :disabled="loadingMore">
        {{ loadingMore ? 'Carregando...' : 'Carregar mais' }}
      </button>
    </div>
  </div>
</template>

<style scoped>
.new-post-btn { width: 100%; padding: 0.75rem; margin-bottom: 1.5rem; border: 1px dashed #dbdbdb; border-radius: 8px; background: none; cursor: pointer; }
.new-post-form { display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1.5rem; padding: 1rem; border: 1px solid #dbdbdb; border-radius: 8px; }
.form-actions { display: flex; gap: 0.5rem; }
.error { color: #e0245e; font-size: 0.8rem; }
</style>