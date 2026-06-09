<template>
  <div>
    <h1 class="mb-6 text-2xl font-bold text-stone-800">茶品百科</h1>
    <div v-if="loading" class="flex justify-center py-12 text-stone-500">加载中…</div>
    <div v-else-if="error" class="py-12 text-center text-amber-600">加载失败，请检查后端服务或稍后重试。</div>
    <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <router-link
        v-for="tea in teas"
        :key="tea.id"
        :to="{ path: `/teas/${tea.id}`, query: { from: 'teas' } }"
        class="group overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm transition hover:shadow-md"
      >
        <div class="aspect-[4/3] overflow-hidden bg-stone-100">
          <img :src="teaImageSrc(tea)" :alt="tea.name" class="h-full w-full object-cover transition group-hover:scale-105" @error="onTeaImageError" />
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-stone-800 group-hover:text-tea-700">{{ tea.name }}</h3>
          <p class="mt-1 text-sm text-stone-500">{{ tea.origin || tea.tea_type?.name }}</p>
        </div>
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import api from '../api'

const DEFAULT_TEA_IMG = '/images/teas/default.jpg'
function toImagePath(url: string | null | undefined): string {
  if (!url) return DEFAULT_TEA_IMG
  const path = url.startsWith('http') ? new URL(url).pathname : url
  return path.replace(/\.svg$/i, '.jpg')
}
function teaImageSrc(tea: { image_url?: string | null }) {
  return toImagePath(tea?.image_url) || DEFAULT_TEA_IMG
}
function onTeaImageError(e: Event) {
  const el = e.target as HTMLImageElement
  if (el && !el.dataset.fallback) {
    el.dataset.fallback = '1'
    el.src = DEFAULT_TEA_IMG
  }
}

const teas = ref<any[]>([])
const loading = ref(true)
const error = ref(false)

onMounted(async () => {
  try {
    error.value = false
    const { data } = await api.get('/teas')
    teas.value = data ?? []
  } catch {
    error.value = true
    teas.value = []
  } finally {
    loading.value = false
  }
})
</script>
