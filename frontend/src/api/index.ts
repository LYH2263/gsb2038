import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  headers: { Accept: 'application/json' },
})

let skipAuthRedirect = false

export function setSkipAuthRedirect(v: boolean) {
  skipAuthRedirect = v
}

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  // FormData 需要浏览器自动设置 multipart boundary，避免被默认 JSON header 覆盖导致文件上传失败
  if (typeof FormData !== 'undefined' && config.data instanceof FormData) {
    delete (config.headers as any)['Content-Type']
    delete (config.headers as any)['content-type']
  }
  return config
})

api.interceptors.response.use(
  (r) => r,
  (e) => {
    if (e.response?.status === 401) {
      localStorage.removeItem('token')
      if (!skipAuthRedirect && !window.location.pathname.startsWith('/login')) {
        window.location.href = '/login'
      }
    }
    return Promise.reject(e)
  }
)

export default api
