<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';
import { fetchMovements, fetchProducts, fetchWarehouses } from '../services';
import type { Meta, Movement, Product, Warehouse } from '../types';

/** Параметры фильтрации истории движений. */
interface MovementFilters {
  warehouse_id: number | '';
  product_id: number | '';
  doc_type: string;
  date_from: string;
  date_to: string;
}

const movements = ref<Movement[]>([]);
const meta = ref<Meta>({ current_page: 1, last_page: 1, per_page: 10, total: 0 });
const warehouses = ref<Warehouse[]>([]);
const products = ref<Product[]>([]);
const loading = ref(false);
const error = ref('');

const filters = reactive<MovementFilters>({
  warehouse_id: '',
  product_id: '',
  doc_type: '',
  date_from: '',
  date_to: '',
});

// Читаемые названия типов документов.
const docLabels: Record<string, string> = {
  'App\\Models\\Order': 'Заказ',
  'App\\Models\\Supply': 'Поставка',
  'App\\Models\\Transfer': 'Перемещение',
  'App\\Models\\Product': 'Начальный остаток',
};

function docLabel(t: string): string {
  return docLabels[t] || t;
}

function quantityClass(q: number): string {
  return q < 0 ? 'text-red-600' : 'text-green-600';
}

async function load(page = 1): Promise<void> {
  loading.value = true;
  error.value = '';
  const filterParams: Record<string, string | number> = { ...filters };
  try {
    const res = await fetchMovements(page, meta.value.per_page, filterParams);
    movements.value = res.data;
    meta.value = res.meta;
  } catch (e) {
    error.value = (e as Error).message;
  } finally {
    loading.value = false;
  }
}

function applyFilters(): void {
  load(1);
}

onMounted(async () => {
  try {
    const [wh, pr] = await Promise.all([fetchWarehouses(), fetchProducts()]);
    warehouses.value = wh;
    products.value = pr;
  } catch (e) {
    error.value = (e as Error).message;
  }
  await load(1);
});
</script>

<template>
  <div>
    <h2>История движения товаров</h2>

    <div class="mb-4 rounded-lg bg-white p-5 shadow-sm">
      <div class="flex flex-wrap items-end gap-3">
        <div class="mb-3">
          <label class="mb-1 block text-[13px] font-semibold">Склад</label>
          <select v-model="filters.warehouse_id" class="rounded-md border border-gray-300 px-2.5 py-2 text-sm">
            <option value="">Все</option>
            <option v-for="w in warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="mb-1 block text-[13px] font-semibold">Товар</label>
          <select v-model="filters.product_id" class="rounded-md border border-gray-300 px-2.5 py-2 text-sm">
            <option value="">Все</option>
            <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="mb-1 block text-[13px] font-semibold">Тип документа</label>
          <select v-model="filters.doc_type" class="rounded-md border border-gray-300 px-2.5 py-2 text-sm">
            <option value="">Все</option>
            <option v-for="(label, key) in docLabels" :key="key" :value="key">{{ label }}</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="mb-1 block text-[13px] font-semibold">Дата с</label>
          <input type="date" v-model="filters.date_from" class="rounded-md border border-gray-300 px-2.5 py-2 text-sm" />
        </div>
        <div class="mb-3">
          <label class="mb-1 block text-[13px] font-semibold">Дата по</label>
          <input type="date" v-model="filters.date_to" class="rounded-md border border-gray-300 px-2.5 py-2 text-sm" />
        </div>
        <button class="mb-3 inline-flex items-center rounded-md bg-blue-600 px-3.5 py-2 text-sm font-medium text-white hover:opacity-90" @click="applyFilters">Применить</button>
      </div>
    </div>

    <div v-if="error" class="mb-3 rounded-md bg-red-100 p-2.5 text-sm text-red-700">{{ error }}</div>

    <div class="rounded-lg bg-white p-5 shadow-sm">
      <table class="w-full border-collapse bg-white text-sm">
        <thead>
          <tr>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold">#</th>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold">Дата</th>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold">Товар</th>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold">Склад</th>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold">Документ</th>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold">Изменение</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="m in movements" :key="m.id">
            <td class="border-b border-gray-200 px-3 py-2.5 text-left">{{ m.id }}</td>
            <td class="border-b border-gray-200 px-3 py-2.5 text-left">{{ new Date(m.created_at).toLocaleString('ru-RU') }}</td>
            <td class="border-b border-gray-200 px-3 py-2.5 text-left">{{ m.product?.name }}</td>
            <td class="border-b border-gray-200 px-3 py-2.5 text-left">{{ m.warehouse?.name }}</td>
            <td class="border-b border-gray-200 px-3 py-2.5 text-left">{{ docLabel(m.doc_type) }} ({{ m.doc_id }})</td>
            <td :class="quantityClass(m.quantity)" class="border-b border-gray-200 px-3 py-2.5 text-left">
              {{ m.quantity > 0 ? '+' : '' }}{{ m.quantity }}
            </td>
          </tr>
          <tr v-if="!movements.length && !loading"><td colspan="6" class="px-3 py-2.5 text-center">Движений не найдено</td></tr>
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