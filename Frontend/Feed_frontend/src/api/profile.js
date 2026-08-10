import api from './axios'

export function fetchOwnProfile() {
  return api.get('/profile')
}

export function updateProfile(formData) {
  formData.append('_method', 'PUT')
  return api.post('/profile', formData)
}

export function fetchUserProfile(username) {
  return api.get(`/users/${username}`)
}

export function followUser(username) {
  return api.post(`/users/${username}/follow`)
}

export function unfollowUser(username) {
  return api.delete(`/users/${username}/follow`)
}