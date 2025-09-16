import { createRouter, createWebHistory } from 'vue-router';
import store from '../store';
import Login from '../views/Login.vue';
import StockList from '../views/StockList.vue';
import BulkStock from '../views/BulkStock.vue';

const routes = [
  { path: '/login', component: Login },
  { path: '/', component: StockList, meta: { requiresAuth: true } },
  { path: '/bulk', component: BulkStock, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !store.getters.isAuthenticated) {
    next('/login');
  } else {
    next();
  }
});

export default router;