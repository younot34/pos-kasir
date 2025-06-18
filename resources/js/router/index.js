import { createRouter, createWebHistory } from 'vue-router'
import Food from '../components/Food.vue'
import POSApp from '../components/POSApp.vue'

const routes = [
  { path: '/', redirect: '/food' },
  { path: '/food', component: Food },
  { path: '/transaksi', component: POSApp }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
