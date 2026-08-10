import api from './axios'

export function fetchFeed(page = 1) {
  return api.get('/posts', { params: { page } })
}

export function createPost(formData) {
  return api.post('/posts', formData)
}

export function fetchPost(id) {
  return api.get(`/posts/${id}`)
}

export function deletePost(id) {
  return api.delete(`/posts/${id}`)
}

export function likePost(id) {
  return api.post(`/posts/${id}/like`)
}

export function unlikePost(id) {
  return api.delete(`/posts/${id}/like`)
}

export function fetchComments(id) {
  return api.get(`/posts/${id}/comments`)
}

export function createComment(id, content) {
  return api.post(`/posts/${id}/comments`, { content })
}

export function fetchUserPosts(username, page = 1) {
  return api.get(`/users/${username}/posts`, { params: { page } })
}