<template>
  <div v-if="loading" class="py-12 text-center text-stone-500">加载中…</div>
  <div v-else-if="article">
    <router-link :to="backTo" class="mb-4 inline-block text-sm text-tea-700 hover:underline">{{ backLabel }}</router-link>
    <ArticleContent :title="article.title" :category="article.category" :image-url="article.image_url" :content="article.content" />
  </div>
  <div v-else class="py-12 text-center text-stone-500">
    <p>未找到该茶类</p>
    <router-link :to="backTo" class="mt-2 inline-block text-tea-700 hover:underline">{{ backLabel }}</router-link>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import api from '../api'
import ArticleContent from '../components/ArticleContent.vue'

const route = useRoute()
const slug = computed(() => route.params.slug as string)
const backTo = computed(() => (route.query.from === 'home' ? '/' : '/tea-types'))
const backLabel = computed(() => (route.query.from === 'home' ? '← 返回首页' : '← 返回茶类'))
const article = ref<any>(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const s = typeof slug.value === 'string' ? slug.value : Array.isArray(slug.value) ? slug.value[0] : ''
    if (s) {
      const { data } = await api.get(`/articles/by-slug/${s}`)
      article.value = data
    }
  } catch {
    article.value = null
  } finally {
    loading.value = false
  }
})
</script>
