import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  headers: { Accept: 'application/json' },
})

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
    // 允许调用方通过 config.skipAuthRedirect=true 跳过全局 401 跳登录页逻辑
    // 用于"主动退出"等场景：即便服务端返回非 2xx，也不应被动跳到登录页
    const skipAuthRedirect = (e.config as any)?.skipAuthRedirect === true
    if (e.response?.status === 401 && !skipAuthRedirect) {
      localStorage.removeItem('token')
      if (!window.location.pathname.startsWith('/login')) window.location.href = '/login'
    }
    return Promise.reject(e)
  }
)

export default api
