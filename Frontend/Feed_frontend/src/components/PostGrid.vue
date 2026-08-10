<script setup>
import { STORAGE_URL } from '@/api/axios'

defineProps({ posts: Array })

function mediaUrl(path) {
  return path ? `${STORAGE_URL}/${path}` : null
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
  gap: 4px;
  margin-top: 1.5rem;
}
.grid-item {
  aspect-ratio: 1 / 1;
  display: block;
  overflow: hidden;
}
.grid-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.grid-item-placeholder {
  background: #efefef;
}
</style>