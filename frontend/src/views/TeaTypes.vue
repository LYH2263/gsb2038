<template>
  <div>
    <h1 class="mb-2 text-2xl font-bold text-stone-800">茶类</h1>
    <p class="mb-6 text-stone-500">点击下方卡片进入对应茶类详情页。</p>
    <div v-if="loading" class="py-12 text-center text-stone-500">加载中…</div>
    <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <router-link
        v-for="t in types"
        :key="t.id"
        :to="{ path: `/tea-types/${t.slug}`, query: { from: 'tea-types' } }"
        class="group overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm transition hover:shadow-md"
      >
        <div class="aspect-[4/3] overflow-hidden bg-stone-100">
          <img :src="typeImageSrc(t)" :alt="t.name" class="h-full w-full object-cover transition group-hover:scale-105" @error="onTypeImageError" />
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-stone-800 group-hover:text-tea-700">{{ t.name }}</h3>
          <p class="mt-1 line-clamp-2 text-sm text-stone-500">{{ t.description }}</p>
        </div>
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import api from '../api'

const DEFAULT_TYPE_IMG = '/images/tea-types/green.jpg'
function toTypeImagePath(url: string | null | undefined): string {
  if (!url) return DEFAULT_TYPE_IMG
  const path = url.startsWith('http') ? new URL(url).pathname : url
  return path.replace(/\.svg$/i, '.jpg')
}
function typeImageSrc(t: { image_url?: string | null }) {
  return toTypeImagePath(t?.image_url) || DEFAULT_TYPE_IMG
}
function onTypeImageError(e: Event) {
  const el = e.target as HTMLImageElement
  if (el && !el.dataset.fallback) {
    el.dataset.fallback = '1'
    el.src = DEFAULT_TYPE_IMG
  }
}

const types = ref<any[]>([])
const loading = ref(true)

onMounted(async () => {
  try {
    const { data } = await api.get('/tea-types')
    types.value = data
  } finally {
    loading.value = false
  }
})
</script>
