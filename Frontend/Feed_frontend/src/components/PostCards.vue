<script setup>
import { STORAGE_URL } from '@/api/axios'
import { likePost, unlikePost } from '@/api/posts'

const props = defineProps({ post: Object })

function mediaUrl(path) {
  return path ? `${STORAGE_URL}/${path}` : null
}

async function toggleLike() {
  const wasLiked = props.post.is_liked
  props.post.is_liked = !wasLiked
  props.post.likes_count += wasLiked ? -1 : 1

  try {
    if (wasLiked) {
      await unlikePost(props.post.id)
    } else {
      await likePost(props.post.id)
    }
  } catch {
    props.post.is_liked = wasLiked
    props.post.likes_count += wasLiked ? 1 : -1
  }
}
</script>

<template>
  <article class="post-card">
    <header>
      <router-link :to="{ name: 'user-profile', params: { username: post.user.username } }">
        @{{ post.user.username }}
      </router-link>
    </header>

    <img v-if="mediaUrl(post.media_path)" :src="mediaUrl(post.media_path)" class="media" />

    <div class="actions">
      <button @click="toggleLike" :class="{ liked: post.is_liked }">
        {{ post.is_liked ? '♥' : '♡' }} {{ post.likes_count }}
      </button>
      <router-link :to="{ name: 'post', params: { id: post.id } }">
        💬 {{ post.comments_count }}
      </router-link>
    </div>

    <p v-if="post.caption" class="caption">
      <strong>@{{ post.user.username }}</strong> {{ post.caption }}
    </p>
  </article>
</template>

<style scoped>
.post-card { border: 1px solid #dbdbdb; border-radius: 8px; margin-bottom: 1.5rem; overflow: hidden; }
header { padding: 0.75rem; font-weight: 600; }
header a { color: inherit; text-decoration: none; }
.media { width: 100%; display: block; aspect-ratio: 1 / 1; object-fit: cover; }
.actions { display: flex; gap: 1rem; padding: 0.5rem 0.75rem; }
.actions button { background: none; border: none; cursor: pointer; font-size: 1rem; }
.actions a { color: inherit; text-decoration: none; }
.liked { color: #e0245e; }
.caption { padding: 0 0.75rem 0.75rem; }
</style>