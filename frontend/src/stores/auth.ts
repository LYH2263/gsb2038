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

let isLoggingOut = false

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
    isLoggingOut = true
    try {
      await api.post('/logout')
    } catch {
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
      isLoggingOut = false
    }
  }

  return { token, user, isLoggedIn, isAdmin, setToken, setUser, fetchUser, logout }
})

export function isActiveLogout() {
  return isLoggingOut
}
