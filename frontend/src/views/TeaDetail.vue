<template>
  <div v-if="loading" class="py-12 text-center text-stone-500">加载中…</div>
  <div v-else-if="tea" class="w-full max-w-5xl">
    <router-link :to="backTo" class="mb-6 inline-flex items-center gap-1 text-sm text-tea-700 hover:underline">{{ backLabel }}</router-link>

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-md">
      <!-- 顶部大图：留白 + 圆角 + 茶色描边 -->
      <div class="p-4 sm:p-6">
        <div class="relative overflow-hidden rounded-xl bg-stone-100 shadow-inner ring-1 ring-stone-200/80">
          <div class="aspect-[16/10] sm:aspect-[2/1]">
            <img
              :src="teaImageSrc(tea)"
              :alt="tea.name"
              class="h-full w-full object-cover object-center"
              @error="onTeaImageError"
            />
          </div>
          <div class="absolute inset-0 rounded-xl ring-1 ring-inset ring-white/20 pointer-events-none" aria-hidden="true" />
        </div>
      </div>

      <div class="grid gap-8 p-6 sm:p-8 lg:grid-cols-[1fr,1.2fr]">
        <!-- 左侧：基本信息 -->
        <div class="space-y-6">
          <div>
            <h1 class="text-2xl font-bold text-stone-800 sm:text-3xl">{{ tea.name }}</h1>
            <p class="mt-2 flex flex-wrap items-center gap-2 text-stone-500">
              <span>{{ tea.origin }}</span>
              <span v-if="tea.origin && tea.tea_type">·</span>
              <router-link
                v-if="tea.tea_type"
                :to="`/tea-types/${tea.tea_type.slug}`"
                class="text-tea-700 hover:underline"
              >
                {{ tea.tea_type.name }}
              </router-link>
            </p>
          </div>

          <div class="rounded-xl border border-stone-200 bg-stone-50/80 p-4">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-stone-500">基本信息</h2>
            <dl class="space-y-2 text-sm">
              <div class="flex justify-between gap-4">
                <dt class="text-stone-500">产地</dt>
                <dd class="font-medium text-stone-800">{{ tea.origin || '—' }}</dd>
              </div>
              <div class="flex justify-between gap-4">
                <dt class="text-stone-500">茶类</dt>
                <dd class="font-medium text-stone-800">
                  <template v-if="tea.tea_type">
                    <router-link :to="`/tea-types/${tea.tea_type.slug}`" class="text-tea-700 hover:underline">
                      {{ tea.tea_type.name }}
                    </router-link>
                  </template>
                  <span v-else>—</span>
                </dd>
              </div>
            </dl>
          </div>

          <div v-if="auth.isAdmin" class="flex flex-wrap gap-3">
            <router-link :to="`/teas/${tea.id}/edit`" class="rounded-lg bg-tea-700 px-4 py-2 text-white hover:bg-tea-800">编辑</router-link>
            <button type="button" class="rounded-lg border border-red-300 px-4 py-2 text-red-600 hover:bg-red-50" @click="onDelete">删除</button>
          </div>
        </div>

        <!-- 右侧：描述与扩展内容 -->
        <div class="space-y-6">
          <section v-if="tea.description" class="rounded-xl border border-stone-200 bg-white p-5">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-stone-500">简介</h2>
            <p class="whitespace-pre-wrap text-stone-600 leading-relaxed">{{ tea.description }}</p>
          </section>

          <section class="rounded-xl border border-stone-200 bg-tea-50/50 p-5">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-tea-800">冲泡建议</h2>
            <p class="text-stone-600 leading-relaxed">{{ tea.brewing_tip || brewingTip }}</p>
          </section>

          <section class="rounded-xl border border-stone-200 bg-stone-50/80 p-5">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-stone-500">品鉴要点</h2>
            <template v-if="tea.tasting_notes">
              <p class="whitespace-pre-wrap text-sm text-stone-600">{{ tea.tasting_notes }}</p>
            </template>
            <ul v-else class="space-y-2 text-sm text-stone-600">
              <li><span class="font-medium text-stone-700">外形：</span>{{ tastingNotes.outline }}</li>
              <li><span class="font-medium text-stone-700">汤色：</span>{{ tastingNotes.liquor }}</li>
              <li><span class="font-medium text-stone-700">香气：</span>{{ tastingNotes.aroma }}</li>
              <li><span class="font-medium text-stone-700">滋味：</span>{{ tastingNotes.taste }}</li>
            </ul>
          </section>
        </div>
      </div>
    </div>

    <!-- 同茶类入口 -->
    <div v-if="tea.tea_type" class="mt-8 rounded-xl border border-stone-200 bg-stone-50/80 px-5 py-4">
      <p class="text-sm text-stone-600">
        同属
        <router-link :to="`/tea-types/${tea.tea_type.slug}`" class="font-medium text-tea-700 hover:underline">
          {{ tea.tea_type.name }}
        </router-link>
        ，可前往茶类页查看更多相关茶品。
      </p>
    </div>

    <!-- 评论区 -->
    <section class="mt-10">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-stone-800">评论留言</h2>
        <span class="text-sm text-stone-500">{{ comments.length }} 条</span>
      </div>

      <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
        <div v-if="auth.isLoggedIn" class="space-y-3">
          <textarea
            v-model="newComment"
            rows="3"
            maxlength="500"
            placeholder="写下你的想法（最多 500 字）"
            class="w-full rounded-xl border border-stone-300 px-4 py-3 text-stone-700 focus:border-tea-700 focus:outline-none focus:ring-1 focus:ring-tea-700"
          />
          <div class="flex items-center justify-between">
            <p class="text-xs text-stone-500">{{ (newComment || '').trim().length }}/500</p>
            <button
              type="button"
              class="rounded-lg bg-tea-700 px-4 py-2 text-sm text-white hover:bg-tea-800 disabled:cursor-not-allowed disabled:opacity-60"
              :disabled="commentSubmitting || !(newComment || '').trim()"
              @click="submitComment"
            >
              {{ commentSubmitting ? '发送中…' : '发送评论' }}
            </button>
          </div>
          <p v-if="commentError" class="text-sm text-red-600">{{ commentError }}</p>
        </div>
        <div v-else class="flex flex-wrap items-center justify-between gap-2 rounded-xl bg-stone-50 p-4">
          <p class="text-sm text-stone-600">登录后即可发表评论。</p>
          <router-link :to="{ path: '/login', query: { redirect: route.fullPath } }" class="text-sm font-medium text-tea-700 hover:underline">去登录 →</router-link>
        </div>

        <div class="mt-6 border-t border-stone-200 pt-6">
          <div v-if="commentsLoading" class="py-6 text-center text-stone-500">加载评论中…</div>
          <div v-else-if="comments.length === 0" class="py-6 text-center text-stone-500">还没有评论，来抢沙发吧。</div>
          <ul v-else class="space-y-4">
            <li v-for="c in comments" :key="c.id" class="rounded-xl border border-stone-200 bg-stone-50/60 p-4">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="flex flex-wrap items-center gap-2">
                    <span class="font-medium text-stone-800">{{ c.user?.name || '匿名用户' }}</span>
                    <span v-if="c.user?.role === 'admin'" class="rounded bg-tea-100 px-2 py-0.5 text-xs font-medium text-tea-800">管理员</span>
                    <span class="text-xs text-stone-500">{{ formatTime(c.created_at) }}</span>
                  </div>
                  <p class="mt-2 whitespace-pre-wrap break-words text-sm leading-relaxed text-stone-700">{{ c.content }}</p>
                </div>
                <button
                  v-if="canDeleteComment(c)"
                  type="button"
                  class="shrink-0 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs text-red-600 hover:bg-red-50"
                  @click="deleteComment(c)"
                >
                  删除
                </button>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </section>
  </div>
  <div v-else class="py-12 text-center text-stone-500">茶品不存在</div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../api'
import { useAuthStore } from '../stores/auth'

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

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const tea = ref<any>(null)
const loading = ref(true)

type TeaComment = {
  id: number
  tea_id: number
  user_id: number
  content: string
  created_at: string
  user?: { id: number; name: string; role?: string }
}

const comments = ref<TeaComment[]>([])
const commentsLoading = ref(true)
const newComment = ref('')
const commentSubmitting = ref(false)
const commentError = ref('')

const backTo = computed(() => (route.query.from === 'home' ? '/' : '/teas'))
const backLabel = computed(() => (route.query.from === 'home' ? '← 返回首页' : '← 返回茶品百科'))

const brewingTip = computed(() => {
  const typeName = tea.value?.tea_type?.name ?? ''
  if (/绿/i.test(typeName)) return '建议水温 80–85℃，茶水比约 1:50，冲泡 2–3 分钟，可多次续水。'
  if (/红/i.test(typeName)) return '建议水温 95–100℃，茶水比约 1:40，冲泡 3–5 分钟。'
  if (/白|黄|青|乌/i.test(typeName)) return '建议水温 90–95℃，茶水比约 1:30，冲泡时间可随个人口味调整。'
  return '建议水温 85–90℃，茶水比约 1:50，冲泡 3–5 分钟，可根据个人口味调整投茶量与时间。'
})

const tastingNotes = computed(() => {
  const d = tea.value?.description ?? ''
  const extract = (key: string, fallback: string) => {
    const m = d.match(new RegExp(key + '[：:]\\s*([^。\\n]+)'))
    return (m?.[1]?.trim() || fallback).slice(0, 50)
  }
  return {
    outline: extract('外形', '条索匀整，形态自然。'),
    liquor: extract('汤色', '清澈明亮。'),
    aroma: extract('香气', '清香持久。'),
    taste: extract('滋味', '醇和回甘。'),
  }
})

onMounted(async () => {
  try {
    const { data } = await api.get(`/teas/${route.params.id}`)
    tea.value = data
  } catch {
    tea.value = null
  } finally {
    loading.value = false
  }

  await fetchComments()
})

async function fetchComments() {
  commentsLoading.value = true
  try {
    const { data } = await api.get(`/teas/${route.params.id}/comments`)
    comments.value = Array.isArray(data) ? data : []
  } catch {
    comments.value = []
  } finally {
    commentsLoading.value = false
  }
}

function formatTime(iso: string | null | undefined): string {
  if (!iso) return ''
  const d = new Date(iso)
  if (Number.isNaN(d.getTime())) return iso
  return d.toLocaleString()
}

function canDeleteComment(c: TeaComment): boolean {
  const uid = auth.user?.id
  if (!uid) return false
  return auth.isAdmin || c.user_id === uid
}

async function submitComment() {
  commentError.value = ''
  const content = (newComment.value || '').trim()
  if (!content) return
  commentSubmitting.value = true
  try {
    const { data } = await api.post(`/teas/${route.params.id}/comments`, { content })
    if (data) comments.value = [data, ...comments.value]
    newComment.value = ''
  } catch (e: any) {
    commentError.value = e.response?.data?.message || '发送失败，请稍后重试'
  } finally {
    commentSubmitting.value = false
  }
}

async function deleteComment(c: TeaComment) {
  if (!confirm('确定删除该评论？')) return
  try {
    await api.delete(`/comments/${c.id}`)
    comments.value = comments.value.filter((x) => x.id !== c.id)
  } catch (e: any) {
    alert(e.response?.data?.message || '删除失败')
  }
}

async function onDelete() {
  if (!tea.value || !confirm('确定删除该茶品？')) return
  try {
    await api.delete(`/teas/${tea.value.id}`)
    await router.push(backTo.value)
  } catch (e: any) {
    alert(e.response?.data?.message || '删除失败')
  }
}
</script>
