<!-- src/views/Profile.vue -->
<script setup>
import { ref, computed, onMounted } from 'vue'
import { fetchOwnProfile, updateProfile } from '@/api/profile'
import { fetchUserPosts } from '@/api/posts'
import { STORAGE_URL } from '@/api/axios'
import Avatar from '@/components/Avatar.vue'
import PostGrid from '@/components/PostGrid.vue'

const BIO_MAX_LENGTH = 500

const profile = ref(null)
const loading = ref(true)
const editing = ref(false)
const saving = ref(false)
const errors = ref({})
const form = ref({ name: '', username: '', bio: '' })
const avatarFile = ref(null)
const avatarPreview = ref(null)
const fileInput = ref(null)

const posts = ref([])
const postsPage = ref(1)
const postsLastPage = ref(1)

const bioLength = computed(() => form.value.bio.length)

async function loadPosts(page = 1) {
  const { data } = await fetchUserPosts(profile.value.user.username, page)
  posts.value = page === 1 ? data.data : [...posts.value, ...data.data]
  postsPage.value = data.current_page
  postsLastPage.value = data.last_page
}

async function load() {
  loading.value = true
  const { data } = await fetchOwnProfile()
  profile.value = data
  form.value = { name: data.user.name, username: data.user.username, bio: data.user.bio || '' }
  loading.value = false
  await loadPosts(1)
}

function openEdit() {
  form.value = { name: profile.value.user.name, username: profile.value.user.username, bio: profile.value.user.bio || '' }
  avatarFile.value = null
  avatarPreview.value = null
  errors.value = {}
  editing.value = true
}

function closeEdit() {
  editing.value = false
}

function triggerFileSelect() {
  fileInput.value.click()
}

function handleFileChange(event) {
  const file = event.target.files[0]
  if (!file) return
  avatarFile.value = file
  avatarPreview.value = URL.createObjectURL(file)
}

async function handleSave() {
  errors.value = {}
  saving.value = true

  const formData = new FormData()
  formData.append('name', form.value.name)
  formData.append('username', form.value.username)
  formData.append('bio', form.value.bio)
  if (avatarFile.value) formData.append('avatar', avatarFile.value)

  try {
    await updateProfile(formData)
    await load()
    editing.value = false
  } catch (err) {
    if (err.response?.status === 422) errors.value = err.response.data.errors || {}
  } finally {
    saving.value = false
  }
}

onMounted(load)
</script>

<template>
  <div v-if="loading">Carregando...</div>

  <div v-else-if="profile" class="content-profile">
    <div class="profile-card">
      <Avatar :name="profile.user.name" :avatar-path="profile.user.avatar_path" :size="150" />

      <div class="profile-info">
        <h1>{{ profile.user.name }}</h1>
        <p class="username">@{{ profile.user.username }}</p>
        <p v-if="profile.user.bio" class="bio">{{ profile.user.bio }}</p>

        <div class="stats">
          <div><strong>{{ profile.posts_count }}</strong><span>posts</span></div>
          <div><strong>{{ profile.followers_count }}</strong><span>seguidores</span></div>
          <div><strong>{{ profile.following_count }}</strong><span>seguindo</span></div>
        </div>
      </div>

      <button class="edit-btn" @click="openEdit">
        <i class="bi bi-pencil"></i>
        Editar perfil
      </button>
    </div>

    <div class="section-header">
      <i class="bi bi-grid-3x3-gap"></i>
      <h2>Publicações</h2>
    </div>

    <PostGrid :posts="posts" />
    <button v-if="postsPage < postsLastPage" class="load-more-btn" @click="loadPosts(postsPage + 1)">
      Carregar mais posts
    </button>

    <!-- Modal de edição -->
    <div v-if="editing" class="modal-overlay" @click.self="closeEdit">
      <div class="modal-card">
        <div class="modal-header">
          <h2>Editar perfil</h2>
          <button class="close-btn" @click="closeEdit">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <form @submit.prevent="handleSave" class="modal-body">
          <div class="avatar-row">
            <div class="avatar-preview-wrapper">
              <img v-if="avatarPreview" :src="avatarPreview" class="avatar-preview" />
              <Avatar v-else :name="profile.user.name" :avatar-path="profile.user.avatar_path" :size="72" />
              <button type="button" class="camera-btn" @click="triggerFileSelect">
                <i class="bi bi-camera-fill"></i>
              </button>
            </div>
            <button type="button" class="change-photo-btn" @click="triggerFileSelect">Trocar foto</button>
            <input ref="fileInput" type="file" accept="image/*" class="hidden-input" @change="handleFileChange" />
          </div>

          <label>
            Nome
            <input v-model="form.name" type="text" />
            <span v-if="errors.name" class="error">{{ errors.name[0] }}</span>
          </label>

          <label>
            Nome de usuário
            <input v-model="form.username" type="text" />
            <span v-if="errors.username" class="error">{{ errors.username[0] }}</span>
          </label>

          <label>
            Bio
            <textarea v-model="form.bio" rows="3" :maxlength="BIO_MAX_LENGTH" />
            <div class="bio-footer">
              <span v-if="errors.bio" class="error">{{ errors.bio[0] }}</span>
              <span class="char-count">{{ bioLength }}/{{ BIO_MAX_LENGTH }}</span>
            </div>
          </label>

          <div class="modal-actions">
            <button type="button" class="cancel-btn" @click="closeEdit">Cancelar</button>
            <button type="submit" class="save-btn" :disabled="saving">
              {{ saving ? 'Salvando...' : 'Salvar alterações' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.content-profile {
  max-width: 48rem;
  margin: 0 auto;
}

.profile-card {
  display: flex;
  align-items: center;
  gap: 1.8rem;
  padding: 1.8rem;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-card);
  flex-wrap: wrap;
}

.profile-info {
  flex: 1;
  min-width: 200px;
}

.profile-info h1 {
  margin: 0;
  font-size: 1.3rem;
}

.username {
  color: var(--color-text-muted);
  margin: 0.15rem 0 0.6rem;
}

.bio {
  margin: 0 0 1rem;
  font-size: 0.9rem;
}

.stats {
  display: flex;
  gap: 2rem;
}

.stats div {
  display: flex;
  flex-direction: column;
}

.stats strong {
  font-size: 1.1rem;
}

.stats span {
  font-size: 0.78rem;
  color: var(--color-text-muted);
}

.edit-btn {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  background: var(--color-surface);
  color: var(--color-text);
  border: 1px solid var(--color-border);
  border-radius: 0.5rem;
  padding: 10px 18px;
  font-family: inherit;
  font-size: 0.9375rem;
  font-weight: 600;
  cursor: pointer;
  align-self: flex-start;
  transition: background-color 0.2s, color 0.2s, box-shadow 0.2s, transform 0.2s;
}

.edit-btn:hover {
  border-color: var(--color-primary);
  color: var(--color-primary);
}

.edit-btn i{
  width: 16px;
  height: 16px;
}

.section-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 2rem 0 1rem;
  color: var(--color-text-muted);
}

.section-header {
  width: 18px;
  height: 18px;
}

.section-header h2 {
  font-size: 0.9rem;
  margin: 0;
  font-weight: 600;
}

.load-more-btn {
  display: block;
  width: 100%;
  margin-top: 1rem;
  padding: 0.7rem;
  border: 1px solid var(--color-border);
  border-radius: 8px;
  background: var(--color-surface);
  cursor: pointer;
  font-family: inherit;
}


/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
  padding: 1rem;
}
.modal-card {
  width: 420px;
  max-width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  background: var(--color-surface);
  border-radius: var(--radius);
  box-shadow: var(--shadow-card);
}
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid var(--color-border);
}
.modal-header h2 { 
  font-size: 1.05rem; 
  margin: 0; 
}
.close-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--color-text-muted);
  display: flex;
}
.close-btn  { 
  width: 20px; 
  height: 20px; 
}

.modal-body {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
}
.avatar-row {
  display: flex;
  align-items: center;
  gap: 1rem;
}
.avatar-preview-wrapper { position: relative; }
.avatar-preview {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--color-avatar-ring);
}
.camera-btn {
  position: absolute;
  bottom: -2px;
  right: -2px;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: var(--color-primary);
  border: 2px solid var(--color-surface);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
.camera-btn { 
  width: 13px; 
  height: 13px; 
}
.change-photo-btn {
  background: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: 8px;
  padding: 0.55rem 1rem;
  font-family: inherit;
  font-size: 0.85rem;
  cursor: pointer;
  color: var(--color-text);
}
.hidden-input { 
  display: none; 
}
label { 
  display: flex; 
  flex-direction: column; 
  gap: 0.35rem; 
  font-size: 0.85rem; 
  color: var(--color-text-muted); 
}
input, textarea {
  padding: 0.65rem 0.75rem;
  border: 1px solid var(--color-border);
  border-radius: 8px;
  font-family: inherit;
  font-size: 0.9rem;
  color: var(--color-text);
  background: var(--color-surface);
}
input:focus, textarea:focus {
  outline: none;
  border-color: var(--color-primary);
}
.bio-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
}
.char-count { font-size: 0.75rem; color: var(--color-text-muted); }
.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.6rem;
  margin-top: 0.5rem;
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
.save-btn {
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.6rem 1.2rem;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
}
.save-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.error { color: var(--color-error); font-size: 0.8rem; }
</style>