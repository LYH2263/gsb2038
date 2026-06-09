<template>
  <article class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
    <div v-if="imageUrl" class="w-full overflow-hidden bg-stone-100">
      <img :src="imageUrl" :alt="title" class="h-auto w-full max-h-[70vh] object-contain" />
    </div>
    <div class="p-8">
      <h1 class="text-2xl font-bold text-stone-800">{{ title }}</h1>
      <p v-if="category" class="mt-2 text-stone-500">{{ category }}</p>
      <div class="prose mt-6 max-w-none article-body">
        <template v-for="(block, i) in contentBlocks">
          <h3 v-if="block.type === 'section'" :key="'s'+i" class="mt-6 text-lg font-semibold text-stone-700">{{ block.text }}</h3>
          <p v-else-if="block.type === 'paragraph'" :key="'p'+i" class="mt-2 whitespace-pre-wrap text-stone-600">{{ block.text }}</p>
          <div v-else-if="block.type === 'image'" :key="'i'+i" class="my-6 overflow-hidden rounded-lg bg-stone-100">
            <img :src="block.src" :alt="block.alt || ''" class="h-auto w-full max-h-[50vh] object-contain" />
          </div>
        </template>
      </div>
    </div>
  </article>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  title: string
  category?: string
  imageUrl?: string | null
  content?: string | null
}>()

const contentBlocks = computed(() => {
  const raw = props.content || ''
  const lines = raw.split('\n')
  const blocks: { type: 'section' | 'paragraph' | 'image'; text?: string; src?: string; alt?: string }[] = []
  for (const line of lines) {
    const imgMatch = line.match(/^\[IMG:(.+?)\](?:\s*-\s*(.+))?$/)
    if (imgMatch) {
      blocks.push({ type: 'image', src: imgMatch[1].trim(), alt: imgMatch[2]?.trim() || '' })
    } else {
      const m = line.match(/^【(.+)】$/)
      if (m) {
        blocks.push({ type: 'section', text: m[1] })
      } else if (line.trim()) {
        blocks.push({ type: 'paragraph', text: line })
      }
    }
  }
  return blocks
})
</script>
