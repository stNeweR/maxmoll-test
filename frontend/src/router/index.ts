import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router';
import OrdersView from '../views/OrdersView.vue';
import OrderFormView from '../views/OrderFormView.vue';
import MovementsView from '../views/MovementsView.vue';

const routes: RouteRecordRaw[] = [
  { path: '/', redirect: '/orders' },
  { path: '/orders', name: 'orders', component: OrdersView },
  { path: '/orders/new', name: 'order-create', component: OrderFormView },
  { path: '/orders/:id/edit', name: 'order-edit', component: OrderFormView, props: true },
  { path: '/movements', name: 'movements', component: MovementsView },
];

export default createRouter({
  history: createWebHistory(),
  routes,
});