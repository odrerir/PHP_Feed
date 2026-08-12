<script setup>
import { STORAGE_URL } from '@/api/axios';

defineProps({ posts: Array });

function mediaUrl(path) {
  return path ? `${STORAGE_URL}/${path}` : null;
}
</script>

<template>
  <div class="post-grid">
    <router-link
      v-for="post in posts"
      :key="post.id"
      :to="{ name: 'post', params: { id: post.id } }"
      class="grid-item"
    >
      <img v-if="mediaUrl(post.media_path)" :src="mediaUrl(post.media_path)" />
      <div v-else class="grid-item-placeholder" />
    </router-link>
  </div>
</template>

<style scoped>
.post-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 6px;
}
.grid-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center top;
  display: block;
  transition: transform 0.2s ease;
}
.grid-item:hover img {
  transform: scale(1.05);
}
.grid-item {
  aspect-ratio: 1 / 1;
  display: block;
  overflow: hidden;
  border-radius: 6px;
  position: relative;
}
.grid-item-placeholder {
  background: var(--color-background);
}
</style>
