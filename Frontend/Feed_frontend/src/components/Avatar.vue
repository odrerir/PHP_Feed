<script setup>
import { computed } from 'vue';
import { STORAGE_URL } from '@/api/axios';

const props = defineProps({
  name: { type: String, default: '' },
  avatarPath: { type: String, default: null },
  size: { type: Number, default: 44 },
});

const colors = ['#f6d9a0', '#f4b8c8', '#c9c2f0', '#bcd4f6', '#c8e6c9'];

const initials = computed(() => props.name.trim().slice(0, 2).toUpperCase());

const bgColor = computed(() => {
  const code = props.name.charCodeAt(0) || 0;
  return colors[code % colors.length];
});

const imageUrl = computed(() => (props.avatarPath ? `${STORAGE_URL}/${props.avatarPath}` : null));
</script>

<template>
  <img
    v-if="imageUrl"
    :src="imageUrl"
    class="avatar"
    :style="{ width: size + 'px', height: size + 'px' }"
  />
  <div
    v-else
    class="avatar avatar-fallback"
    :style="{
      width: size + 'px',
      height: size + 'px',
      background: bgColor,
      fontSize: size * 0.38 + 'px',
    }"
  >
    {{ initials }}
  </div>
</template>

<style scoped>
.avatar {
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
  border: 2px solid var(--color-avatar-ring);
}
.avatar-fallback {
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-text);
  font-weight: 600;
}
</style>
