<template>
  <div class="mx-auto max-w-sm rounded-2xl border border-stone-200 bg-white p-8 shadow-sm">
    <h2 class="mb-6 text-xl font-semibold text-stone-800">注册</h2>
    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="mb-1 block text-sm text-stone-600">昵称</label>
        <input
          v-model="name"
          type="text"
          required
          placeholder="请输入昵称"
          class="w-full rounded-lg border px-3 py-2 focus:outline-none focus:ring-1"
          :class="fieldErrors.name ? 'border-red-400 focus:border-red-500 focus:ring-red-400' : 'border-stone-300 focus:border-tea-700 focus:ring-tea-700'"
        />
        <p v-if="fieldErrors.name" class="mt-1 text-xs text-red-600">{{ fieldErrors.name }}</p>
      </div>
      <div>
        <label class="mb-1 block text-sm text-stone-600">邮箱</label>
        <input
          v-model="email"
          type="email"
          required
          placeholder="请输入邮箱"
          class="w-full rounded-lg border px-3 py-2 focus:outline-none focus:ring-1"
          :class="fieldErrors.email ? 'border-red-400 focus:border-red-500 focus:ring-red-400' : 'border-stone-300 focus:border-tea-700 focus:ring-tea-700'"
        />
        <p v-if="fieldErrors.email" class="mt-1 text-xs text-red-600">{{ fieldErrors.email }}</p>
      </div>
      <div>
        <label class="mb-1 block text-sm text-stone-600">密码</label>
        <input
          v-model="password"
          type="password"
          required
          minlength="6"
          placeholder="请输入密码（至少 6 位）"
          class="w-full rounded-lg border px-3 py-2 focus:outline-none focus:ring-1"
          :class="fieldErrors.password ? 'border-red-400 focus:border-red-500 focus:ring-red-400' : 'border-stone-300 focus:border-tea-700 focus:ring-tea-700'"
        />
        <p class="mt-0.5 text-xs text-stone-500">密码至少 6 位</p>
        <p v-if="fieldErrors.password" class="mt-1 text-xs text-red-600">{{ fieldErrors.password }}</p>
      </div>
      <div>
        <label class="mb-1 block text-sm text-stone-600">确认密码</label>
        <input
          v-model="password_confirmation"
          type="password"
          required
          placeholder="请再次输入密码"
          class="w-full rounded-lg border px-3 py-2 focus:outline-none focus:ring-1"
          :class="fieldErrors.password_confirmation ? 'border-red-400 focus:border-red-500 focus:ring-red-400' : 'border-stone-300 focus:border-tea-700 focus:ring-tea-700'"
        />
        <p v-if="fieldErrors.password_confirmation" class="mt-1 text-xs text-red-600">{{ fieldErrors.password_confirmation }}</p>
      </div>
      <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
      <button type="submit" class="w-full rounded-lg bg-tea-700 py-2.5 text-white transition hover:bg-tea-800" :disabled="loading">{{ loading ? '注册中…' : '注册' }}</button>
    </form>
    <p class="mt-4 text-center text-sm text-stone-500">
      <router-link to="/login" class="text-tea-700 hover:underline">已有账号？登录</router-link>
    </p>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'

const router = useRouter()
const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const error = ref('')
const fieldErrors = ref<Record<string, string>>({})
const loading = ref(false)

function validate(): boolean {
  const err: Record<string, string> = {}
  if (!name.value.trim()) err.name = '请输入昵称'
  if (!email.value.trim()) err.email = '请输入邮箱'
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) err.email = '请输入有效的邮箱地址'
  if (!password.value) err.password = '请输入密码'
  else if (password.value.length < 6) err.password = '密码至少 6 位'
  if (!password_confirmation.value) err.password_confirmation = '请再次输入密码'
  else if (password.value !== password_confirmation.value) err.password_confirmation = '两次密码不一致'
  fieldErrors.value = err
  return Object.keys(err).length === 0
}

async function submit() {
  error.value = ''
  fieldErrors.value = {}
  if (!validate()) return
  loading.value = true
  try {
    await api.post('/register', { name: name.value, email: email.value, password: password.value, password_confirmation: password_confirmation.value })
    await router.push({ path: '/login', query: { registered: '1' } })
    if (typeof window !== 'undefined') window.alert('注册成功，请登录')
  } catch (e: any) {
    const err = e.response?.data
    const msg = err?.message || (err?.errors ? ([] as string[]).concat(...(Object.values(err.errors) as string[][])).join(' ') : '注册失败')
    error.value = msg
    if (err?.errors) {
      const per: Record<string, string> = {}
      if (Array.isArray(err.errors.email)) per.email = err.errors.email[0]
      if (Array.isArray(err.errors.name)) per.name = err.errors.name[0]
      if (Array.isArray(err.errors.password)) per.password = err.errors.password[0]
      if (Object.keys(per).length) fieldErrors.value = { ...fieldErrors.value, ...per }
    }
  } finally {
    loading.value = false
  }
}
</script>
