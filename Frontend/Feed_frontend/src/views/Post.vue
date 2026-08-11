<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { fetchPost, deletePost, fetchComments, createComment, likePost, unlikePost } from '@/api/posts'
import { STORAGE_URL } from '@/api/axios'
import { useAuthStore } from '@/stores/auth'
import DefaultAvatar from '@/assets/profile/Avatar.svg'

const props = defineProps(['id'])
const router = useRouter()
const auth = useAuthStore()

const post = ref(null)
const comments = ref([])
const loading = ref(true)
const newComment = ref('')
const commenting = ref(false)
const deleting = ref(false)

function mediaUrl(path) {
  return path ? `${STORAGE_URL}/${path}` : null
}

function avatarUrl(path) {
  return path ? `${STORAGE_URL}/${path}` : DefaultAvatar
}

async function load() {
  loading.value = true
  const [postRes, commentsRes] = await Promise.all([
    fetchPost(props.id),
    fetchComments(props.id),
  ])
  post.value = postRes.data
  comments.value = commentsRes.data
  loading.value = false
}

async function toggleLike() {
  const wasLiked = post.value.is_liked
  post.value.is_liked = !wasLiked
  post.value.likes_count += wasLiked ? -1 : 1

  try {
    if (wasLiked) await unlikePost(props.id)
    else await likePost(props.id)
  } catch {
    post.value.is_liked = wasLiked
    post.value.likes_count += wasLiked ? 1 : -1
  }
}

async function handleComment() {
  if (!newComment.value.trim()) return
  commenting.value = true

  try {
    const { data } = await createComment(props.id, newComment.value)
    comments.value.unshift(data)
    newComment.value = ''
    post.value.comments_count += 1
  } finally {
    commenting.value = false
  }
}

async function handleDelete() {
  if (!confirm('Excluir este post?')) return
  deleting.value = true

  try {
    await deletePost(props.id)
    router.push({ name: 'home' })
  } finally {
    deleting.value = false
  }
}

onMounted(load)
watch(() => props.id, load)
</script>

<template>
  <div v-if="loading">Carregando...</div>

  <div v-else-if="post">
    <header class="post-header">
      <router-link :to="{ name: 'user-profile', params: { username: post.user.username } }" class="post-user">
        <img :src="avatarUrl(post.user.avatar_path)" class="post-avatar" />
        <span>@{{ post.user.username }}</span>
      </router-link>
      <button v-if="post.user.id === auth.user?.id" @click="handleDelete" :disabled="deleting" class="delete-btn">
        Excluir
      </button>
    </header>

    <img v-if="mediaUrl(post.media_path)" :src="mediaUrl(post.media_path)" class="media" />

    <div class="actions">
      <button @click="toggleLike" :class="{ liked: post.is_liked }">
        {{ post.is_liked ? '♥' : '♡' }} {{ post.likes_count }}
      </button>
      <span>💬 {{ post.comments_count }}</span>
    </div>

    <p v-if="post.caption" class="caption">{{ post.caption }}</p>

    <section class="comments">
      <form @submit.prevent="handleComment" class="comment-form">
        <input v-model="newComment" type="text" placeholder="Adicione um comentário..." />
        <button type="submit" :disabled="commenting">Enviar</button>
      </form>

      <p v-if="comments.length === 0">Nenhum comentário ainda.</p>
      <div v-for="comment in comments" :key="comment.id" class="comment">
        <img :src="avatarUrl(comment.user.avatar_path)" class="comment-avatar" />
        <strong>@{{ comment.user.username }}</strong> {{ comment.content }}
      </div>
    </section>
  </div>
</template>

<style scoped>
.post-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  font-weight: 600;
}

.post-header a {
  color: inherit;
  text-decoration: none;
}

.delete-btn {
  background: none;
  border: 1px solid #e0245e;
  color: #e0245e;
  border-radius: 4px;
  padding: 0.25rem 0.6rem;
  cursor: pointer;
}

.media {
  width: 100%;
  border-radius: 8px;
}

.actions {
  display: flex;
  gap: 1rem;
  padding: 0.5rem 0;
}

.actions button {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1rem;
}

.liked {
  color: #e0245e;
}

.caption {
  margin-bottom: 1rem;
}

.comments {
  border-top: 1px solid #dbdbdb;
  padding-top: 1rem;
}

.comment-form {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.post-user {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: inherit;
  text-decoration: none;
}

.post-avatar {
  width: 3.5rem;
  height: 3.5rem;
  border-radius: 50%;
  object-fit: cover;
}

.comment-form input {
  flex: 1;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.comment {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.4rem 0;
  font-size: 0.9rem;
}

.comment-avatar {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  object-fit: cover;
}
</style>