import api from './axios'

export function fetchHome(page = 1) {
  return api.get('/home', { params: { page } })
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

export function fetchComments(id, page = 1) {
  return api.get(`/posts/${id}/comments`, { params: { page } })
}

export function createComment(id, content) {
  return api.post(`/posts/${id}/comments`, { content })
}

export function fetchUserPosts(username, page = 1) {
  return api.get(`/users/${username}/posts`, { params: { page } })
}