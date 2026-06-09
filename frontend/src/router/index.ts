import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'Home', component: () => import('../views/Home.vue') },
    { path: '/login', name: 'Login', component: () => import('../views/Login.vue') },
    { path: '/register', name: 'Register', component: () => import('../views/Register.vue') },
    { path: '/culture', name: 'Culture', component: () => import('../views/ArticlePage.vue'), meta: { slug: 'culture' } },
    { path: '/history', name: 'History', component: () => import('../views/ArticlePage.vue'), meta: { slug: 'history' } },
    { path: '/ceremony', name: 'Ceremony', component: () => import('../views/ArticlePage.vue'), meta: { slug: 'ceremony' } },
    { path: '/health', name: 'Health', component: () => import('../views/ArticlePage.vue'), meta: { slug: 'health' } },
    { path: '/tea-types', name: 'TeaTypes', component: () => import('../views/TeaTypes.vue') },
    { path: '/tea-types/:slug', name: 'TeaTypeDetail', component: () => import('../views/TeaTypeDetail.vue') },
    { path: '/teas', name: 'Teas', component: () => import('../views/Teas.vue') },
    { path: '/teas/create', name: 'TeaCreate', component: () => import('../views/TeaForm.vue'), meta: { mode: 'create', requiresAuth: true, requiresAdmin: true } },
    { path: '/teas/:id/edit', name: 'TeaEdit', component: () => import('../views/TeaForm.vue'), meta: { mode: 'edit', requiresAuth: true, requiresAdmin: true } },
    { path: '/teas/:id', name: 'TeaDetail', component: () => import('../views/TeaDetail.vue') },
    { path: '/about', name: 'About', component: () => import('../views/ArticlePage.vue'), meta: { slug: 'about' } },
  ],
})

router.beforeEach(async (to, _from, next) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.isLoggedIn) {
    next({ path: '/login', query: { redirect: to.fullPath } })
    return
  }
  if (to.meta.requiresAdmin) {
    if (!auth.user && auth.token) await auth.fetchUser()
    if (!auth.isAdmin) {
      next('/')
      return
    }
  }
  next()
})

export default router
