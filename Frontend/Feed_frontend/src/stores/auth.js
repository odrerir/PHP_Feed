import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '@/api/axios';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const token = ref(localStorage.getItem('auth_token'));
  let fetchUserPromise = null;

  const isAuthenticated = computed(() => !!token.value);

  async function register(data) {
    const response = await api.post('/register', data);
    setSession(response.data);
  }

  async function login(credentials) {
    const response = await api.post('/login', credentials);
    setSession(response.data);
  }

  async function logout() {
    try {
      await api.post('/logout');
    } finally {
      clearSession();
    }
  }

  async function fetchUser() {
    if (user.value) return; 
    if (fetchUserPromise) return fetchUserPromise; 

    fetchUserPromise = api.get('/me').then(response => {
      user.value = response.data;
      fetchUserPromise = null;
    });

    return fetchUserPromise;
  }

  function setUser(updatedUser) {
    user.value = updatedUser;
  }

  function setSession(data) {
    user.value = data.user;
    token.value = data.token;
    localStorage.setItem('auth_token', data.token);
  }

  function clearSession() {
    user.value = null;
    token.value = null;
    localStorage.removeItem('auth_token');
  }

  return { user, token, isAuthenticated, register, login, logout, fetchUser, setUser };
});
