<template>
  <div class="flex min-h-screen flex-col bg-stone-50 text-stone-800">
    <header class="sticky top-0 z-10 shrink-0 border-b border-stone-200 bg-white/90 backdrop-blur shadow-sm">
      <nav class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-2 px-4 py-3">
        <router-link to="/" class="text-xl font-semibold text-tea-800">茶文化</router-link>
        <div class="flex flex-wrap items-center gap-2">
          <div class="flex flex-wrap gap-1">
            <router-link to="/" exact-active-class="nav-link-active" class="nav-link">首页</router-link>
            <router-link to="/culture" active-class="nav-link-active" class="nav-link">茶文化</router-link>
            <router-link to="/history" active-class="nav-link-active" class="nav-link">茶史</router-link>
            <router-link to="/tea-types" active-class="nav-link-active" class="nav-link">茶类</router-link>
            <router-link to="/ceremony" active-class="nav-link-active" class="nav-link">茶道</router-link>
            <router-link to="/health" active-class="nav-link-active" class="nav-link">茶与健康</router-link>
            <router-link to="/teas" active-class="nav-link-active" class="nav-link">茶品百科</router-link>
            <router-link to="/about" active-class="nav-link-active" class="nav-link">关于</router-link>
          </div>
          <span v-if="auth.isLoggedIn" class="h-5 w-px bg-stone-200" aria-hidden="true" />
          <div v-if="auth.isLoggedIn" class="flex flex-wrap gap-2">
            <router-link v-if="auth.isAdmin" to="/teas/create" active-class="nav-btn-create-active" class="nav-btn nav-btn-create">新增茶品</router-link>
            <button type="button" class="nav-btn nav-btn-logout" @click="logout">退出</button>
          </div>
          <template v-else>
            <span class="h-5 w-px bg-stone-200" aria-hidden="true" />
            <div class="flex flex-wrap gap-2">
              <router-link to="/login" active-class="nav-btn-login-active" class="nav-btn nav-btn-login">登录</router-link>
              <router-link to="/register" class="nav-btn nav-btn-register">注册</router-link>
            </div>
          </template>
        </div>
      </nav>
    </header>
    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>
    <footer class="mt-auto shrink-0 border-t border-stone-200 bg-stone-100 py-6 text-center text-sm text-stone-500">
      茶文化 · 仅供学习展示
    </footer>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useAuthStore } from './stores/auth'

const auth = useAuthStore()

onMounted(() => {
  if (auth.token && !auth.user) auth.fetchUser()
})

function logout() {
  auth.logout()
  window.location.href = '/'
}
</script>

<style scoped>
.nav-link {
  @apply rounded px-3 py-1.5 text-stone-600 transition hover:bg-stone-100 hover:text-tea-700;
}
.nav-link-active {
  @apply border-b-2 border-tea-700 bg-tea-50 font-semibold text-tea-800;
}
/* 登录、注册、退出、新增茶品：按钮样式，与导航链接区分 */
.nav-btn {
  @apply inline-flex items-center justify-center rounded-lg border px-4 py-2 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-tea-700 focus:ring-offset-1;
}
.nav-btn-login {
  @apply border-stone-300 bg-white text-stone-700 hover:bg-stone-50 hover:border-stone-400;
}
.nav-btn-login-active {
  @apply border-tea-700 bg-tea-50 text-tea-800;
}
.nav-btn-register {
  @apply border-tea-700 bg-tea-700 text-white hover:bg-tea-800 hover:border-tea-800;
}
.nav-btn-create {
  @apply border-tea-700 bg-tea-700 text-white hover:bg-tea-800 hover:border-tea-800;
}
.nav-btn-create-active {
  @apply border-tea-700 bg-tea-700 text-white;
}
.nav-btn-logout {
  @apply border-stone-300 bg-white text-stone-600 hover:bg-stone-100 hover:border-stone-400 hover:text-stone-800;
}
.fade-enter-active,
.fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from,
.fade-leave-to { opacity: 0; }
</style>
