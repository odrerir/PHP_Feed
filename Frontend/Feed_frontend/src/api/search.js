import api from './axios'

export function searchUsers(query, page = 1) {
  return api.get('/search', { params: { q: query, page } })
}