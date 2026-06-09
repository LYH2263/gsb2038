<template>
  <section class="space-y-12">
    <div class="rounded-2xl bg-gradient-to-br from-tea-50 to-stone-100 p-8 shadow-sm">
      <h1 class="mb-4 text-3xl font-bold text-stone-800">茶文化</h1>
      <p class="mb-2 max-w-xl text-stone-600">茶源于中国，兴于唐、盛于宋，从陆羽《茶经》到今日百姓生活，一脉相承。</p>
      <p class="max-w-xl text-stone-600">品茶、识茶、习茶，传承千年茶道。</p>
    </div>

    <div>
      <h2 class="mb-4 text-xl font-semibold text-stone-800">探索茶世界</h2>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <router-link to="/culture" class="group rounded-xl border border-stone-200 bg-white p-4 shadow-sm transition hover:shadow-md">
          <h3 class="font-semibold text-stone-800 group-hover:text-tea-700">茶文化概览</h3>
          <p class="mt-1 text-sm text-stone-500">源流与传承</p>
        </router-link>
        <router-link to="/history" class="group rounded-xl border border-stone-200 bg-white p-4 shadow-sm transition hover:shadow-md">
          <h3 class="font-semibold text-stone-800 group-hover:text-tea-700">茶史</h3>
          <p class="mt-1 text-sm text-stone-500">千年茶路</p>
        </router-link>
        <router-link to="/tea-types" class="group rounded-xl border border-stone-200 bg-white p-4 shadow-sm transition hover:shadow-md">
          <h3 class="font-semibold text-stone-800 group-hover:text-tea-700">茶类</h3>
          <p class="mt-1 text-sm text-stone-500">六大茶类</p>
        </router-link>
        <router-link to="/ceremony" class="group rounded-xl border border-stone-200 bg-white p-4 shadow-sm transition hover:shadow-md">
          <h3 class="font-semibold text-stone-800 group-hover:text-tea-700">茶道</h3>
          <p class="mt-1 text-sm text-stone-500">和敬清寂</p>
        </router-link>
        <router-link to="/health" class="group rounded-xl border border-stone-200 bg-white p-4 shadow-sm transition hover:shadow-md">
          <h3 class="font-semibold text-stone-800 group-hover:text-tea-700">茶与健康</h3>
          <p class="mt-1 text-sm text-stone-500">饮茶与养生</p>
        </router-link>
        <router-link to="/teas" class="group rounded-xl border border-stone-200 bg-white p-4 shadow-sm transition hover:shadow-md">
          <h3 class="font-semibold text-stone-800 group-hover:text-tea-700">茶品百科</h3>
          <p class="mt-1 text-sm text-stone-500">名茶一览</p>
        </router-link>
      </div>
    </div>

    <div v-if="teas.length" class="rounded-xl border border-stone-200 bg-white p-6 shadow-sm">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-stone-800">精选茶品</h2>
        <router-link to="/teas" class="text-sm text-tea-700 hover:underline">查看全部 →</router-link>
      </div>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <router-link v-for="tea in teas.slice(0, 8)" :key="tea.id" :to="{ path: `/teas/${tea.id}`, query: { from: 'home' } }" class="group overflow-hidden rounded-lg border border-stone-200 transition hover:shadow">
          <div class="aspect-[4/3] bg-stone-100">
            <img :src="teaImageSrc(tea)" :alt="tea.name" class="h-full w-full object-cover transition group-hover:scale-105" @error="onTeaImageError" />
          </div>
          <div class="p-3">
            <h3 class="font-medium text-stone-800 group-hover:text-tea-700">{{ tea.name }}</h3>
            <p class="text-sm text-stone-500">{{ tea.origin || tea.tea_type?.name }}</p>
          </div>
        </router-link>
      </div>
    </div>

    <div class="rounded-xl border border-stone-200 bg-tea-50/50 p-6 shadow-sm">
      <h2 class="mb-4 text-xl font-semibold text-stone-800">茶之四德</h2>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg bg-white p-4 shadow-sm"><div class="mb-2 h-10 w-10 rounded-full bg-tea-100 flex items-center justify-center text-tea-700 font-bold">和</div><p class="text-sm text-stone-600">和谐与共</p></div>
        <div class="rounded-lg bg-white p-4 shadow-sm"><div class="mb-2 h-10 w-10 rounded-full bg-tea-100 flex items-center justify-center text-tea-700 font-bold">敬</div><p class="text-sm text-stone-600">恭敬之心</p></div>
        <div class="rounded-lg bg-white p-4 shadow-sm"><div class="mb-2 h-10 w-10 rounded-full bg-tea-100 flex items-center justify-center text-tea-700 font-bold">清</div><p class="text-sm text-stone-600">清心寡欲</p></div>
        <div class="rounded-lg bg-white p-4 shadow-sm"><div class="mb-2 h-10 w-10 rounded-full bg-tea-100 flex items-center justify-center text-tea-700 font-bold">寂</div><p class="text-sm text-stone-600">寂然悟道</p></div>
      </div>
    </div>

    <blockquote class="border-l-4 border-tea-700 bg-white py-2 pl-6 pr-4 text-stone-600 shadow-sm">茶之为饮，发乎神农氏，闻于鲁周公，兴于唐而盛于宋。—— 陆羽《茶经》</blockquote>

    <div>
      <h2 class="mb-4 text-xl font-semibold text-stone-800">六大茶类</h2>
      <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        <router-link v-for="t in teaTypes" :key="t.id" :to="{ path: `/tea-types/${t.slug}`, query: { from: 'home' } }" class="rounded-lg border border-stone-200 bg-white p-4 transition hover:border-tea-700 hover:shadow">
          <h3 class="font-semibold text-stone-800">{{ t.name }}</h3>
          <p class="mt-1 text-sm text-stone-500">{{ t.description }}</p>
        </router-link>
      </div>
    </div>

    <div class="rounded-xl border border-stone-200 bg-stone-50 p-8 text-center">
      <p class="mb-4 text-stone-600">开始你的茶文化之旅</p>
      <div class="flex flex-wrap justify-center gap-4">
        <router-link to="/teas" class="rounded-lg bg-tea-700 px-5 py-2.5 text-white hover:bg-tea-800">浏览茶品百科</router-link>
        <router-link to="/culture" class="rounded-lg border border-stone-300 px-5 py-2.5 text-stone-700 hover:bg-white">茶文化概览</router-link>
      </div>
    </div>
  </section>
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
const teaTypes = ref<any[]>([])

onMounted(async () => {
  try {
    const [t1, t2] = await Promise.all([api.get('/teas').then(r => r.data), api.get('/tea-types').then(r => r.data)])
    teas.value = t1
    teaTypes.value = t2
  } catch {}
})
</script>
