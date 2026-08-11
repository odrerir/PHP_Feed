<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { fetchPost, deletePost, fetchComments, createComment, likePost, unlikePost } from '@/api/posts'
import { STORAGE_URL } from '@/api/axios'
import { useAuthStore } from '@/stores/auth'
import { timeAgo } from '@/lib/time'
import Avatar from '@/components/Avatar.vue'

const props = defineProps(['id'])
const router = useRouter()
const auth = useAuthStore()

const post = ref(null)
const comments = ref([])
const loading = ref(true)
const newComment = ref('')
const commenting = ref(false)
const deleting = ref(false)
const commentsPage = ref(1)
const commentsLastPage = ref(1)

function mediaUrl(path) {
  return path ? `${STORAGE_URL}/${path}` : null
}

async function load() {
  loading.value = true
  const [postRes, commentsRes] = await Promise.all([
    fetchPost(props.id),
    fetchComments(props.id),
  ])
  post.value = postRes.data
  comments.value = commentsRes.data.data
  commentsPage.value = commentsRes.data.current_page
  commentsLastPage.value = commentsRes.data.last_page
  loading.value = false
}

async function loadMoreComments() {
  const { data } = await fetchComments(props.id, commentsPage.value + 1)
  comments.value = [...comments.value, ...data.data]
  commentsPage.value = data.current_page
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
    comments.value.push(data)
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

  <div v-else-if="post" class="post-detail">
    <div class="media-side">
      <img v-if="mediaUrl(post.media_path)" :src="mediaUrl(post.media_path)" />
    </div>

    <div class="panel">
      <header class="panel-header">
        <Avatar :name="post.user.name" :avatar-path="post.user.avatar_path" :size="40" />
        <div class="author-info">
          <router-link :to="{ name: 'user-profile', params: { username: post.user.username } }" class="author-name">
            {{ post.user.name }}
          </router-link>
          <span>@{{ post.user.username }}</span>
        </div>
        <button v-if="post.user.id === auth.user?.id" class="delete-btn" @click="handleDelete" :disabled="deleting">
          <i class="bi bi-trash"></i>
        </button>
      </header>

      <div class="panel-body">
        <div class="comment-row">
          <Avatar :name="post.user.name" :avatar-path="post.user.avatar_path" :size="34" />
          <div class="comment-content">
            <p><strong>{{ post.user.username }}</strong> {{ post.caption }}</p>
            <span class="comment-time">{{ timeAgo(post.created_at) }}</span>
          </div>
        </div>

        <div v-for="comment in comments" :key="comment.id" class="comment-row">
          <Avatar :name="comment.user.name" :avatar-path="comment.user.avatar_path" :size="34" />
          <div class="comment-content">
            <p><strong>{{ comment.user.username }}</strong> {{ comment.content }}</p>
            <span class="comment-time">{{ timeAgo(comment.created_at) }}</span>
          </div>
        </div>
        <button v-if="commentsPage < commentsLastPage" class="load-more-comments" @click="loadMoreComments">
          Carregar mais comentários
        </button>
      </div>

      <div class="panel-actions">
        <button class="icon-btn" :class="{ liked: post.is_liked }" @click="toggleLike">
          <i :class="post.is_liked ? 'bi bi-heart-fill' : 'bi bi-heart'"></i>
          {{ post.likes_count }}
        </button>
        <span class="icon-btn">
          <i class="bi bi-chat"></i>
          {{ post.comments_count }}
        </span>
      </div>

      <form class="panel-input" @submit.prevent="handleComment">
        <Avatar :name="auth.user?.name || ''" :avatar-path="auth.user?.avatar_path" :size="32" />
        <input v-model="newComment" type="text" placeholder="Escreva algo gentil..." />
        <button type="submit" :disabled="commenting">
          <i class="bi bi-send-fill"></i>
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped>
.post-detail {
  display: grid;
  grid-template-columns: 1fr 380px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-card);
  overflow: hidden;
  min-height: 500px;
}
.media-side {
  background: var(--color-background);
  display: flex;
  align-items: center;
}
.media-side img {
  width: 100%;
  display: block;
}
.panel {
  display: flex;
  flex-direction: column;
  border-left: 1px solid var(--color-border);
}
.panel-header {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  padding: 1rem;
  border-bottom: 1px solid var(--color-border);
}
.author-info { 
  flex: 1; 
  display: flex; 
  flex-direction: column; 
}

.author-name {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--color-text);
  text-decoration: none;
  width: fit-content;
  transition: color 0.15s;
}

.author-name:hover { 
  color: var(--color-primary); 
}

.author-info span { 
  font-size: 0.78rem; 
  color: var(--color-text-muted); 
}
.delete-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--color-text-muted);
  font-size: 1.1rem;
}

.delete-btn:hover { 
  color: var(--color-error); 
}

.panel-body {
  flex: 1;
  overflow-y: auto;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
}

.comment-row { 
  display: flex; 
  gap: 0.6rem; 
}

.comment-content p { 
  margin: 0; 
  font-size: 0.88rem; 
  line-height: 1.4; 
}

.comment-content strong { 
  margin-right: 0.3rem; 
}

.comment-time { 
  font-size: 0.75rem; 
  color: var(--color-text-muted); 
}

.panel-actions {
  display: flex;
  align-items: center;
  gap: 1.1rem;
  padding: 0.8rem 1rem;
  border-top: 1px solid var(--color-border);
}

.icon-btn {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  background: none;
  border: none;
  cursor: pointer;
  font-family: inherit;
  font-size: 0.85rem;
  color: var(--color-text);
}

.icon-btn i { 
  font-size: 1.25rem; 
  color: var(--color-text-muted); 
}

.icon-btn.liked i { 
  color: var(--color-accent); 
}

.panel-input {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.9rem 1rem;
  border-top: 1px solid var(--color-border);
}
.panel-input input {
  flex: 1;
  border: 1px solid var(--color-border);
  border-radius: 999px;
  padding: 0.55rem 1rem;
  font-family: inherit;
  font-size: 0.85rem;
  background: var(--color-background);
}
.panel-input input:focus {
  outline: none;
  border-color: var(--color-primary);
}
.panel-input button {
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  flex-shrink: 0;
}
.panel-input button:disabled { opacity: 0.6; cursor: not-allowed; }
.panel-input button i { font-size: 0.95rem; }

@media (max-width: 720px) {
  .post-detail { grid-template-columns: 1fr; }
  .panel { border-left: none; border-top: 1px solid var(--color-border); }
}
</style>