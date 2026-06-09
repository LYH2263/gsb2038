<template>
  <div class="w-full max-w-5xl">
    <h2 class="mb-6 text-xl font-bold text-stone-800">{{ isEdit ? '编辑茶品' : '新增茶品' }}</h2>
    <form @submit.prevent="submit" class="grid gap-4 rounded-xl border border-stone-200 bg-white p-6 shadow-sm lg:grid-cols-2 lg:gap-6">
      <div class="space-y-4">
        <div>
          <label class="mb-1 block text-sm text-stone-600">名称 *</label>
          <input v-model="form.name" type="text" required class="w-full rounded-lg border border-stone-300 px-3 py-2 focus:border-tea-700 focus:outline-none focus:ring-1 focus:ring-tea-700" />
        </div>
        <div>
          <label class="mb-1 block text-sm text-stone-600">产地</label>
          <input v-model="form.origin" type="text" class="w-full rounded-lg border border-stone-300 px-3 py-2 focus:border-tea-700 focus:outline-none focus:ring-1 focus:ring-tea-700" />
        </div>
        <div>
          <label class="mb-1 block text-sm text-stone-600">茶类</label>
          <select v-model="form.tea_type_id" class="w-full rounded-lg border border-stone-300 px-3 py-2 focus:border-tea-700 focus:outline-none focus:ring-1 focus:ring-tea-700">
            <option value="">请选择茶类（可选）</option>
            <option v-for="t in teaTypes" :key="t.id" :value="t.id">{{ t.name }}</option>
          </select>
        </div>
        <div>
          <label class="mb-1 block text-sm text-stone-600">茶品图片</label>
          <div class="flex flex-wrap items-start gap-4">
            <div class="shrink-0">
              <input
                ref="fileInputRef"
                type="file"
                accept="image/jpeg,image/png,image/gif,image/webp"
                class="hidden"
                @change="onImageSelect"
              />
              <button
                type="button"
                class="rounded-lg border border-stone-300 bg-stone-50 px-4 py-2 text-sm text-stone-700 hover:bg-stone-100 focus:outline-none focus:ring-1 focus:ring-tea-700"
                @click="fileInputRef?.click()"
              >
                {{ imageFile ? '重新选择' : '选择本地图片' }}
              </button>
              <button
                v-if="imageFile"
                type="button"
                class="ml-2 rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm text-stone-600 hover:bg-stone-50 focus:outline-none focus:ring-1 focus:ring-tea-700"
                @click="clearSelectedFile"
              >
                取消本地图片
              </button>
            </div>
            <div v-if="previewSrc" class="h-24 w-24 overflow-hidden rounded-lg border border-stone-200 bg-stone-100">
              <img :src="previewSrc" alt="预览" class="h-full w-full object-cover" />
            </div>
            <p v-if="imageFile" class="text-sm text-stone-500">{{ imageFile.name }}（{{ formatFileSize(imageFile.size) }}）</p>
          </div>
          <p class="mt-1 text-xs text-stone-500">支持 JPG、PNG、GIF、WebP，不超过 2MB（仅支持上传图片）。</p>
        </div>
      </div>

      <div class="space-y-4">
        <div>
          <label class="mb-1 block text-sm text-stone-600">描述</label>
          <textarea v-model="form.description" rows="7" class="w-full rounded-lg border border-stone-300 px-3 py-2 focus:border-tea-700 focus:outline-none focus:ring-1 focus:ring-tea-700" placeholder="茶品简介、产地与历史等"></textarea>
        </div>
        <div>
          <label class="mb-1 block text-sm text-stone-600">冲泡建议</label>
          <textarea v-model="form.brewing_tip" rows="3" class="w-full rounded-lg border border-stone-300 px-3 py-2 focus:border-tea-700 focus:outline-none focus:ring-1 focus:ring-tea-700" placeholder="如：建议水温 80–85℃，茶水比约 1:50，冲泡 2–3 分钟"></textarea>
        </div>
        <div>
          <label class="mb-1 block text-sm text-stone-600">品鉴要点</label>
          <textarea v-model="form.tasting_notes" rows="4" class="w-full rounded-lg border border-stone-300 px-3 py-2 focus:border-tea-700 focus:outline-none focus:ring-1 focus:ring-tea-700" placeholder="可填写外形、汤色、香气、滋味等，每行一条或分段"></textarea>
        </div>
      </div>

      <p v-if="error" class="text-sm text-red-600 lg:col-span-2">{{ error }}</p>
      <div class="flex gap-3 lg:col-span-2">
        <button type="submit" class="rounded-lg bg-tea-700 px-4 py-2 text-white hover:bg-tea-800" :disabled="loading">{{ loading ? '提交中…' : '保存' }}</button>
        <router-link :to="isEdit ? `/teas/${route.params.id}` : '/teas'" class="rounded-lg border border-stone-300 px-4 py-2 text-stone-600 hover:bg-stone-50">取消</router-link>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted, onUnmounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../api'

const route = useRoute()
const router = useRouter()
const isEdit = computed(() => route.meta.mode === 'edit')
const form = reactive({
  name: '',
  origin: '',
  description: '',
  tea_type_id: null as number | null,
  brewing_tip: '',
  tasting_notes: '',
})
const teaTypes = ref<{ id: number; name: string }[]>([])
const error = ref('')
const loading = ref(false)
const fileInputRef = ref<HTMLInputElement | null>(null)
const imageFile = ref<File | null>(null)
const imagePreview = ref<string | null>(null)
const existingImageUrl = ref<string>('')

const previewSrc = computed(() => {
  if (imagePreview.value) return imagePreview.value
  const url = (existingImageUrl.value || '').trim()
  return url ? url : null
})

function formatFileSize(bytes: number): string {
  if (bytes < 1024) return bytes + ' B'
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB'
  return (bytes / (1024 * 1024)).toFixed(1) + ' MB'
}

function onImageSelect(e: Event) {
  const input = e.target as HTMLInputElement
  const file = input.files?.[0]
  imageFile.value = file ?? null
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value)
    imagePreview.value = null
  }
  if (file) {
    imagePreview.value = URL.createObjectURL(file)
  }
}

function clearSelectedFile() {
  imageFile.value = null
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value)
    imagePreview.value = null
  }
}

onMounted(async () => {
  try {
    const { data: types } = await api.get('/tea-types')
    teaTypes.value = types
  } catch {}
  if (isEdit.value && route.params.id) {
    const { data } = await api.get(`/teas/${route.params.id}`)
    form.name = data.name
    form.origin = data.origin ?? ''
    existingImageUrl.value = data.image_url ?? ''
    form.description = data.description ?? ''
    form.tea_type_id = data.tea_type_id ?? null
    form.brewing_tip = data.brewing_tip ?? ''
    form.tasting_notes = data.tasting_notes ?? ''
  }
})

onUnmounted(() => {
  if (imagePreview.value && imagePreview.value.startsWith('blob:')) {
    URL.revokeObjectURL(imagePreview.value)
  }
})

function buildFormData(): FormData {
  const fd = new FormData()
  fd.append('name', form.name)
  fd.append('origin', form.origin || '')
  fd.append('description', form.description || '')
  fd.append('brewing_tip', form.brewing_tip || '')
  fd.append('tasting_notes', form.tasting_notes || '')
  const tid = form.tea_type_id === '' || form.tea_type_id == null ? '' : String(form.tea_type_id)
  fd.append('tea_type_id', tid)
  if (imageFile.value) {
    fd.append('image', imageFile.value)
  }
  return fd
}

async function submit() {
  error.value = ''
  if (!form.name.trim()) {
    error.value = '请填写茶品名称'
    return
  }
  loading.value = true
  try {
    const teaTypeId = (form.tea_type_id === '' || form.tea_type_id == null) ? null : Number(form.tea_type_id)
    const payload = {
      name: form.name,
      origin: form.origin || null,
      description: form.description || null,
      tea_type_id: Number.isFinite(teaTypeId as any) ? teaTypeId : null,
      brewing_tip: form.brewing_tip || null,
      tasting_notes: form.tasting_notes || null,
    }
    if (imageFile.value) {
      const formData = buildFormData()
      if (isEdit.value) {
        formData.append('_method', 'PUT')
        await api.post(`/teas/${route.params.id}`, formData)
        await router.push(`/teas/${route.params.id}`)
      } else {
        await api.post('/teas', formData)
        await router.push('/teas')
      }
    } else if (isEdit.value) {
      await api.put(`/teas/${route.params.id}`, payload)
      await router.push(`/teas/${route.params.id}`)
    } else {
      await api.post('/teas', payload)
      await router.push('/teas')
    }
  } catch (e: any) {
    const status = e.response?.status
    const err = e.response?.data
    if (status === 401) {
      error.value = '请先登录后再保存。'
      return
    }
    if (status === 403) {
      error.value = '仅管理员可新增或编辑茶品。'
      return
    }
    error.value = err?.message || (err?.errors ? ([] as string[]).concat(...(Object.values(err.errors || {}) as string[][])).join(' ') : '保存失败')
  } finally {
    loading.value = false
  }
}
</script>
