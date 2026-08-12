<script setup>
import { ref, onMounted, watch } from 'vue';
import { fetchUserProfile, followUser, unfollowUser } from '@/api/profile';
import { fetchUserPosts } from '@/api/posts';
import Avatar from '@/components/Avatar.vue';
import PostGrid from '@/components/PostGrid.vue';
import Spinner from '@/components/Spinner.vue';

const props = defineProps(['username']);

const profile = ref(null);
const loading = ref(true);

const posts = ref([]);
const postsPage = ref(1);
const postsLastPage = ref(1);

async function loadPosts(page = 1) {
  const { data } = await fetchUserPosts(props.username, page);
  posts.value = page === 1 ? data.data : [...posts.value, ...data.data];
  postsPage.value = data.current_page;
  postsLastPage.value = data.last_page;
}

async function load() {
  loading.value = true;
  const { data } = await fetchUserProfile(props.username);
  profile.value = data;
  loading.value = false;
  await loadPosts(1);
}

async function toggleFollow() {
  const wasFollowing = profile.value.is_following;
  profile.value.is_following = !wasFollowing;
  profile.value.followers_count += wasFollowing ? -1 : 1;

  try {
    if (wasFollowing) await unfollowUser(props.username);
    else await followUser(props.username);
  } catch {
    profile.value.is_following = wasFollowing;
    profile.value.followers_count += wasFollowing ? 1 : -1;
  }
}

onMounted(load);
watch(
  () => props.username,
  () => {
    postsPage.value = 1;
    load();
  }
);
</script>

<template>
  <Spinner v-if="loading" />

  <div v-else-if="profile" class="content-profile">
    <div class="profile-card">
      <Avatar :name="profile.user.name" :avatar-path="profile.user.avatar_path" :size="96" />

      <div class="profile-info">
        <h1>{{ profile.user.name }}</h1>
        <p class="username">@{{ profile.user.username }}</p>
        <p v-if="profile.user.bio" class="bio">{{ profile.user.bio }}</p>

        <div class="stats">
          <div>
            <strong>{{ profile.posts_count }}</strong
            ><span>posts</span>
          </div>
          <div>
            <strong>{{ profile.followers_count }}</strong
            ><span>seguidores</span>
          </div>
          <div>
            <strong>{{ profile.following_count }}</strong
            ><span>seguindo</span>
          </div>
        </div>
      </div>

      <button
        v-if="!profile.is_self"
        class="follow-btn"
        :class="{ following: profile.is_following }"
        @click="toggleFollow"
      >
        <i :class="profile.is_following ? 'bi bi-check-lg' : 'bi bi-person-plus'"></i>
        {{ profile.is_following ? 'Seguindo' : 'Seguir' }}
      </button>
    </div>

    <div class="section-header">
      <i class="bi bi-grid-3x3-gap"></i>
      <h2>Publicações</h2>
    </div>

    <PostGrid :posts="posts" />
    <button
      v-if="postsPage < postsLastPage"
      class="load-more-btn"
      @click="loadPosts(postsPage + 1)"
    >
      Carregar mais posts
    </button>
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

.follow-btn {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  border: none;
  border-radius: 8px;
  padding: 0.65rem 1.1rem;
  font-family: inherit;
  font-size: 0.88rem;
  font-weight: 600;
  cursor: pointer;
  align-self: flex-start;
  background: var(--color-primary);
  color: white;
}

.follow-btn i {
  width: 16px;
  height: 16px;
}

.follow-btn.following {
  background: var(--color-background);
  color: var(--color-text);
  border: 1px solid var(--color-border);
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
</style>
