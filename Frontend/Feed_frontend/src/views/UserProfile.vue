<script setup>
import { ref, onMounted, watch } from 'vue'
import { fetchUserProfile, followUser, unfollowUser } from '@/api/profile'
import { STORAGE_URL } from '@/api/axios'

const props = defineProps(['username'])

const profile = ref(null)
const loading = ref(true)
const toggling = ref(false)

async function load() {
  loading.value = true
  const { data } = await fetchUserProfile(props.username)
  profile.value = data
  loading.value = false
}

async function toggleFollow() {
  const wasFollowing = profile.value.is_following

  // atualiza a tela na hora, sem esperar a API
  profile.value.is_following = !wasFollowing
  profile.value.followers_count += wasFollowing ? -1 : 1

  try {
    if (wasFollowing) {
      await unfollowUser(props.username)
    } else {
      await followUser(props.username)
    }
  } catch (err) {
    // caso de erro desfaz a mudança visual
    profile.value.is_following = wasFollowing
    profile.value.followers_count += wasFollowing ? 1 : -1
  }
}

function avatarUrl(path) {
  return path ? `${STORAGE_URL}/${path}` : null
}

onMounted(load)

// re-busca se navegar de um perfil pra outro (ex: clicar em outro username),
// já que o Vue Router reaproveita a mesma instância do componente quando só o :param muda
watch(() => props.username, load)
</script>

<template>
  <div v-if="loading">Carregando...</div>

  <div v-else-if="profile">
    <img
      v-if="avatarUrl(profile.user.avatar_path)"
      :src="avatarUrl(profile.user.avatar_path)"
      class="avatar"
    />
    <div v-else class="avatar avatar-placeholder" />

    <h1>{{ profile.user.name }}</h1>
    <p class="username">@{{ profile.user.username }}</p>
    <p v-if="profile.user.bio">{{ profile.user.bio }}</p>

    <div class="stats">
      <span><strong>{{ profile.posts_count }}</strong> posts</span>
      <span><strong>{{ profile.followers_count }}</strong> seguidores</span>
      <span><strong>{{ profile.following_count }}</strong> seguindo</span>
    </div>

    <button v-if="!profile.is_self" @click="toggleFollow" :disabled="toggling">
      {{ profile.is_following ? 'Deixar de seguir' : 'Seguir' }}
    </button>
  </div>
</template>

<style scoped>
.avatar {
  width: 96px;
  height: 96px;
  border-radius: 50%;
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
</style>