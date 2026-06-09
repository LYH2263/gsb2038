<template>
  <div class="mx-auto max-w-sm rounded-2xl border border-stone-200 bg-white p-8 shadow-sm">
    <h2 class="mb-6 text-xl font-semibold text-stone-800">登录</h2>
    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="mb-1 block text-sm text-stone-600">邮箱</label>
        <input
          v-model="email"
          type="email"
          required
          placeholder="请输入邮箱"
          class="w-full rounded-lg border px-3 py-2 focus:outline-none focus:ring-1"
          :class="error ? 'border-red-400 focus:border-red-500 focus:ring-red-400' : 'border-stone-300 focus:border-tea-700 focus:ring-tea-700'"
        />
        <p v-if="fieldErrors.email" class="mt-1 text-xs text-red-600">{{ fieldErrors.email }}</p>
      </div>
      <div>
        <label class="mb-1 block text-sm text-stone-600">密码</label>
        <input
          v-model="password"
          type="password"
          required
          placeholder="请输入密码"
          class="w-full rounded-lg border px-3 py-2 focus:outline-none focus:ring-1"
          :class="error ? 'border-red-400 focus:border-red-500 focus:ring-red-400' : 'border-stone-300 focus:border-tea-700 focus:ring-tea-700'"
        />
        <p class="mt-0.5 text-xs text-stone-500">密码至少 6 位</p>
        <p v-if="fieldErrors.password" class="mt-1 text-xs text-red-600">{{ fieldErrors.password }}</p>
      </div>
      <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
      <button type="submit" class="w-full rounded-lg bg-tea-700 py-2.5 text-white transition hover:bg-tea-800" :disabled="loading">{{ loading ? '登录中…' : '登录' }}</button>
    </form>
    <p class="mt-4 text-center text-sm text-stone-500">
      <router-link to="/register" class="text-tea-700 hover:underline">注册账号</router-link>
    </p>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../api'
import { useAuthStore } from '../stores/auth'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const email = ref('')
const password = ref('')
const error = ref('')
const fieldErrors = ref<Record<string, string>>({})
const loading = ref(false)

function validate(): boolean {
  const err: Record<string, string> = {}
  if (!email.value.trim()) err.email = '请输入邮箱'
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) err.email = '请输入有效的邮箱地址'
  if (!password.value) err.password = '请输入密码'
  else if (password.value.length < 6) err.password = '密码至少 6 位'
  fieldErrors.value = err
  return Object.keys(err).length === 0
}

async function submit() {
  error.value = ''
  fieldErrors.value = {}
  if (!validate()) return
  loading.value = true
  try {
    const { data } = await api.post<{ token: string; user: { id: number; name: string; email: string } }>('/login', { email: email.value, password: password.value })
    auth.setToken(data.token)
    auth.setUser(data.user)
    // 确保拿到包含 role 的用户信息，避免前端权限判断不准确
    if (!(data.user as any)?.role) await auth.fetchUser()
    const redirect = route.query.redirect as string
    await router.push(redirect && redirect.startsWith('/') ? redirect : '/')
  } catch (e: any) {
    const msg = e.response?.data?.message || '登录失败'
    error.value = msg
    if (/邮箱|email/i.test(msg)) fieldErrors.value = { ...fieldErrors.value, email: msg }
    else if (/密码|password/i.test(msg)) fieldErrors.value = { ...fieldErrors.value, password: msg }
  } finally {
    loading.value = false
  }
}
</script>
