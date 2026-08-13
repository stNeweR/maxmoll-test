<script setup lang="ts">
import { onMounted, reactive, ref, watch } from 'vue';
import { fetchOrders } from '../services';
import type { Meta, Order } from '../types';

/** Параметры фильтрации и пагинации списка заказов. */
interface OrderFilters {
  status: string;
  per_page: number;
}

const orders = ref<Order[]>([]);
const meta = ref<Meta>({ current_page: 1, last_page: 1, per_page: 10, total: 0 });
const filters = reactive<OrderFilters>({ status: '', per_page: 10 });
const loading = ref(false);
const error = ref('');

async function load(page = 1): Promise<void> {
  loading.value = true;
  error.value = '';
  try {
    const res = await fetchOrders(page, filters.status, filters.per_page);
    orders.value = res.data;
    meta.value = res.meta;
  } catch (e) {
    error.value = (e as Error).message;
  } finally {
    loading.value = false;
  }
}

function statusClass(s: string): string {
  const base = 'inline-flex px-2 py-0.5 rounded-full text-xs font-semibold ';
  if (s === 'completed') return base + 'bg-green-100 text-green-700';
  if (s === 'canceled') return base + 'bg-red-100 text-red-700';
  return base + 'bg-blue-100 text-blue-700';
}

onMounted(() => load(1));
watch(() => filters.per_page, () => load(1));
</script>

<template>
  <div>
    <div class="mb-4 flex items-center justify-between rounded-lg bg-white p-5 shadow-sm">
      <div class="flex items-center gap-3">
        <select v-model="filters.status" class="rounded-md border border-gray-300 px-2.5 py-2 text-sm">
          <option value="">Статус: все</option>
          <option value="active">В работе</option>
          <option value="completed">Выполнен</option>
          <option value="canceled">Отменён</option>
        </select>
        <select v-model="filters.per_page" class="rounded-md border border-gray-300 px-2.5 py-2 text-sm">
          <option :value="10">10 на стр.</option>
          <option :value="25">25 на стр.</option>
          <option :value="50">50 на стр.</option>
        </select>
      </div>
      <RouterLink
        to="/orders/new"
        class="inline-flex items-center rounded-md bg-blue-600 px-3.5 py-1.5 text-sm font-medium text-white hover:opacity-90"
      >Создать заказ</RouterLink>
    </div>

    <div v-if="error" class="mb-3 rounded-md bg-red-100 p-2.5 text-sm text-red-700">{{ error }}</div>

    <div class="rounded-lg bg-white p-5 shadow-sm">
      <table class="w-full border-collapse bg-white text-sm">
        <thead>
          <tr>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold">ID</th>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold">Клиент</th>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold">Склад</th>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold">Позиций</th>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold">Сумма</th>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold">Статус</th>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="o in orders" :key="o.id">
            <td class="border-b border-gray-200 px-3 py-2.5 text-left">#{{ o.id }}</td>
            <td class="border-b border-gray-200 px-3 py-2.5 text-left">{{ o.customer?.name }}</td>
            <td class="border-b border-gray-200 px-3 py-2.5 text-left">{{ o.warehouse?.name }}</td>
            <td class="border-b border-gray-200 px-3 py-2.5 text-left">{{ o.items?.length }}</td>
            <td class="border-b border-gray-200 px-3 py-2.5 text-left">{{ o.items?.reduce((s, it) => s + it.count * (it.product?.price ?? 0), 0) }} ₽</td>
            <td class="border-b border-gray-200 px-3 py-2.5 text-left"><span :class="statusClass(o.status)">{{ o.status }}</span></td>
            <td class="border-b border-gray-200 px-3 py-2.5 text-left">
              <RouterLink
                :to="`/orders/${o.id}/edit`"
                class="inline-flex items-center rounded-md bg-gray-500 px-3 py-1 text-sm font-medium text-white hover:opacity-90"
              >Открыть</RouterLink>
            </td>
          </tr>
          <tr v-if="!orders.length && !loading"><td colspan="7" class="px-3 py-2.5 text-center">Заказов нет</td></tr>
        </tbody>
      </table>

      <div class="mt-4 flex items-center gap-2">
        <button
          :disabled="meta.current_page <= 1"
          class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm disabled:opacity-40"
          @click="load(meta.current_page - 1)"
        >Назад</button>
        <span>Стр. {{ meta.current_page }} из {{ meta.last_page }} ({{ meta.total }})</span>
        <button
          :disabled="meta.current_page >= meta.last_page"
          class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm disabled:opacity-40"
          @click="load(meta.current_page + 1)"
        >Вперёд</button>
      </div>
    </div>
  </div>
</template>