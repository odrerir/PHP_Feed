<script setup>
import { STORAGE_URL } from '@/api/axios'
import { likePost, unlikePost } from '@/api/posts'
import { timeAgo } from '@/lib/time'
import Avatar from '@/components/Avatar.vue'

const props = defineProps({ post: Object })

function mediaUrl(path) {
  return path ? `${STORAGE_URL}/${path}` : null
}

async function toggleLike() {
  const wasLiked = props.post.is_liked
  props.post.is_liked = !wasLiked
  props.post.likes_count += wasLiked ? -1 : 1

  try {
    if (wasLiked) await unlikePost(props.post.id)
    else await likePost(props.post.id)
  } catch {
    props.post.is_liked = wasLiked
    props.post.likes_count += wasLiked ? 1 : -1
  }
}
</script>

<template>
  <article class="post-card">
    <header>
      <Avatar :name="post.user.name" :avatar-path="post.user.avatar_path" :size="40" />
      <div class="author-info">
        <router-link :to="{ name: 'user-profile', params: { username: post.user.username } }" class="author-name">
          {{ post.user.name }}
        </router-link>
        <span>@{{ post.user.username }} · {{ timeAgo(post.created_at) }}</span>
      </div>
    </header>

    <img v-if="mediaUrl(post.media_path)" :src="mediaUrl(post.media_path)" class="media" />

    <div class="actions">
      <button @click="toggleLike" :class="{ liked: post.is_liked }" class="icon-btn">
        <svg v-if="post.is_liked" viewBox="0 0 24 24" fill="currentColor"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 1 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 1 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        {{ post.likes_count }}
      </button>

      <router-link :to="{ name: 'post', params: { id: post.id } }" class="icon-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
        {{ post.comments_count }}
      </router-link>

      <svg class="bookmark-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
    </div>

    <p v-if="post.caption" class="caption">
      <strong>{{ post.user.username }}</strong> {{ post.caption }}
    </p>

    <router-link v-if="post.comments_count > 0" :to="{ name: 'post', params: { id: post.id } }" class="view-comments">
      Ver {{ post.comments_count }} comentário{{ post.comments_count > 1 ? 's' : '' }}
    </router-link>
  </article>
</template>

<style scoped>
.post-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-card);
  margin-bottom: 1.5rem;
  overflow: hidden;
}
header {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  padding: 0.9rem 1rem;
}
.author-info {
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
.media {
  width: 100%;
  display: block;
  aspect-ratio: 1 / 1;
  object-fit: cover;
}
.actions {
  display: flex;
  align-items: center;
  gap: 1.1rem;
  padding: 0.7rem 1rem 0.3rem;
}
.icon-btn {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  background: none;
  border: none;
  cursor: pointer;
  font-family: inherit;
  font-size: 0.85rem;
  color: var(--color-text);
  text-decoration: none;
}
.icon-btn svg {
  width: 21px;
  height: 21px;
  color: var(--color-text-muted);
}
.icon-btn.liked,
.icon-btn.liked svg {
  color: var(--color-accent);
}
.bookmark-icon {
  width: 20px;
  height: 20px;
  color: var(--color-text-muted);
  margin-left: auto;
}
.caption {
  padding: 0.2rem 1rem 0.4rem;
  font-size: 0.9rem;
}
.caption strong {
  margin-right: 0.3rem;
}
.view-comments {
  display: block;
  padding: 0 1rem 1rem;
  font-size: 0.82rem;
  color: var(--color-text-muted);
  text-decoration: none;
}
</style>