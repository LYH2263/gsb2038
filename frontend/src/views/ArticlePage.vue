<template>
  <div v-if="loading" class="py-12 text-center text-stone-500">加载中…</div>
  <div v-else-if="article">
    <ArticleContent :title="article.title" :category="article.category" :image-url="article.image_url" :content="article.content" />
  </div>
  <div v-else class="py-12 text-center text-stone-500">内容不存在</div>
</template>

<script setup lang="ts">
import { ref, watch, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../api'
import ArticleContent from '../components/ArticleContent.vue'

const route = useRoute()
const slug = computed(() => (route.meta.slug as string) || route.params.slug)
const article = ref<any>(null)
const loading = ref(true)

async function fetchArticle() {
  const s = typeof slug.value === 'string' ? slug.value : Array.isArray(slug.value) ? slug.value[0] : ''
  loading.value = true
  try {
    if (s) {
      const { data } = await api.get(`/articles/by-slug/${s}`)
      article.value = data
    } else {
      article.value = null
    }
  } catch {
    article.value = null
  } finally {
    loading.value = false
  }
}

onMounted(fetchArticle)
watch(slug, fetchArticle)
</script>
