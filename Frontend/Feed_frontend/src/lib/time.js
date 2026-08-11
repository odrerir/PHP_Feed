export function timeAgo(dateStr) {
  const diffMin = Math.floor((Date.now() - new Date(dateStr).getTime()) / 60000)
  if (diffMin < 1) return 'agora'
  if (diffMin < 60) return `há ${diffMin} min`
  const diffH = Math.floor(diffMin / 60)
  if (diffH < 24) return `há ${diffH} h`
  const diffD = Math.floor(diffH / 24)
  if (diffD < 30) return `há ${diffD} d`
  return new Date(dateStr).toLocaleDateString('pt-BR')
}