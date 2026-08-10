<script setup>
import { ref, onMounted } from 'vue'
import { fetchOwnProfile, updateProfile } from '@/api/profile'
import { STORAGE_URL } from '@/api/axios'
import PostGrid from '@/components/PostGrid.vue'
import { fetchUserPosts } from '@/api/posts'
import DefaultAvatar from '@/assets/profile/Avatar.svg'

const profile = ref(null)
const loading = ref(true)
const editing = ref(false)
const saving = ref(false)
const errors = ref({})
const form = ref({ name: '', username: '', bio: '' })
const avatarFile = ref(null)
const posts = ref([])
const postsPage = ref(1)
const postsLastPage = ref(1)

async function load() {
  loading.value = true
  const { data } = await fetchOwnProfile()
  profile.value = data
  form.value = {
    name: data.user.name,
    username: data.user.username,
    bio: data.user.bio || '',
  }
  loading.value = false
  await loadPosts(1)
}

async function loadPosts(page = 1) {
  const { data } = await fetchUserPosts(profile.value.user.username, page)
  posts.value = page === 1 ? data.data : [...posts.value, ...data.data]
  postsPage.value = data.current_page
  postsLastPage.value = data.last_page
}

function avatarUrl(path) {
  return path ? `${STORAGE_URL}/${path}` : DefaultAvatar
}

function handleFileChange(event) {
  avatarFile.value = event.target.files[0] || null
}

async function handleSave() {
  errors.value = {}
  saving.value = true

  const formData = new FormData()
  formData.append('name', form.value.name)
  formData.append('username', form.value.username)
  formData.append('bio', form.value.bio)
  if (avatarFile.value) {
    formData.append('avatar', avatarFile.value)
  }

  try {
    await updateProfile(formData)
    await load()
    editing.value = false
    avatarFile.value = null
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
    }
  } finally {
    saving.value = false
  }
}

onMounted(load)
</script>

<template>
  <div v-if="loading">Carregando...</div>

  <div v-else-if="profile">
    <img :src="avatarUrl(profile.user.avatar_path)" class="avatar" />

    <h1>{{ profile.user.name }}</h1>
    <p class="username">@{{ profile.user.username }}</p>
    <p v-if="profile.user.bio">{{ profile.user.bio }}</p>

    <div class="stats">
      <span><strong>{{ profile.posts_count }}</strong> posts</span>
      <span><strong>{{ profile.followers_count }}</strong> seguidores</span>
      <span><strong>{{ profile.following_count }}</strong> seguindo</span>
    </div>

    <button v-if="!editing" @click="editing = true">Editar perfil</button>

    <form v-else @submit.prevent="handleSave" class="edit-form">
      <label>
        Nome
        <input v-model="form.name" type="text" />
        <span v-if="errors.name" class="error">{{ errors.name[0] }}</span>
      </label>

      <label>
        Username
        <input v-model="form.username" type="text" />
        <span v-if="errors.username" class="error">{{ errors.username[0] }}</span>
      </label>

      <label>
        Bio
        <textarea v-model="form.bio" rows="3" />
        <span v-if="errors.bio" class="error">{{ errors.bio[0] }}</span>
      </label>

      <label>
        Avatar
        <input type="file" accept="image/*" @change="handleFileChange" />
      </label>

      <div class="edit-actions">
        <button type="submit" :disabled="saving">{{ saving ? 'Salvando...' : 'Salvar' }}</button>
        <button type="button" @click="editing = false">Cancelar</button>
      </div>
    </form>
    <PostGrid :posts="posts" />
    <button v-if="postsPage < postsLastPage" @click="loadPosts(postsPage + 1)">Carregar mais posts</button>
  </div>
</template>

<style scoped>
.avatar {
  width: 96px;
  height: 96px;
  border-radius: 50%;
  border: 1px solid #000000;
  object-fit: cover;
}
.avatar-placeholder {
  background: #dbdbdb;
}
.username {
  color: #8e8e8e;
}
.stats {
  display: flex;
  gap: 1.5rem;
  margin: 1rem 0;
}
.edit-form {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  max-width: 320px;
  margin-top: 1rem;
}
.edit-actions {
  display: flex;
  gap: 0.5rem;
}
.error {
  color: #e0245e;
  font-size: 0.8rem;
}
</style>