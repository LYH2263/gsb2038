import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../api'

export type UserRole = 'user' | 'admin'

export interface AuthUser {
  id: number
  name: string
  email: string
  role: UserRole
}

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem('token'))
  const user = ref<AuthUser | null>(null)

  const isLoggedIn = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  function setToken(t: string) {
    token.value = t
    localStorage.setItem('token', t)
  }

  function setUser(u: AuthUser | null) {
    user.value = u
  }

  async function fetchUser() {
    if (!token.value) return
    try {
      const { data } = await api.get<AuthUser>('/user')
      user.value = data
    } catch {
      setUser(null)
      token.value = null
      localStorage.removeItem('token')
    }
  }

  async function logout() {
    // 必须在清空 token 之前发起请求，否则请求拦截器拿不到 Authorization，
    // 服务端无法识别当前 token，导致旧 token 实际未被吊销。
    if (token.value) {
      try {
        await api.post('/logout', null, { skipAuthRedirect: true } as any)
      } catch {
        // 即便服务端调用失败，也继续完成本地登出
      }
    }
    token.value = null
    user.value = null
    localStorage.removeItem('token')
  }

  return { token, user, isLoggedIn, isAdmin, setToken, setUser, fetchUser, logout }
})
