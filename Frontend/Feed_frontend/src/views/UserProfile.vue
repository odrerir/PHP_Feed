<script setup>
import { ref, onMounted, watch } from 'vue'
import { fetchUserProfile, followUser, unfollowUser } from '@/api/profile'
import { STORAGE_URL } from '@/api/axios'
import PostGrid from '@/components/PostGrid.vue'
import { fetchUserPosts } from '@/api/posts'
import DefaultAvatar from '@/assets/profile/Avatar.svg'

const props = defineProps(['username'])

const profile = ref(null)
const loading = ref(true)
const toggling = ref(false)
const posts = ref([])
const postsPage = ref(1)
const postsLastPage = ref(1)

async function load() {
  loading.value = true
  const { data } = await fetchUserProfile(props.username)
  profile.value = data
  loading.value = false
  await loadPosts(1)
}

async function loadPosts(page = 1) {
  const { data } = await fetchUserPosts(props.username, page)
  posts.value = page === 1 ? data.data : [...posts.value, ...data.data]
  postsPage.value = data.current_page
  postsLastPage.value = data.last_page
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
  return path ? `${STORAGE_URL}/${path}` : DefaultAvatar
}

onMounted(load)

// re-busca se navegar de um perfil pra outro (ex: clicar em outro username),
// já que o Vue Router reaproveita a mesma instância do componente quando só o :param muda
watch(() => props.username, () => {
  postsPage.value = 1
  load()
})
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

    <button v-if="!profile.is_self" @click="toggleFollow" :disabled="toggling">
      {{ profile.is_following ? 'Deixar de seguir' : 'Seguir' }}
    </button>
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
</style>