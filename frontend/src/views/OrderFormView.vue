<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import {
  changeOrderStatus,
  createOrder,
  errorMessage,
  fetchCustomers,
  fetchOrder,
  fetchProducts,
  fetchWarehouses,
  updateOrder,
} from '../services';
import type { Customer, Order, Product, Warehouse } from '../types';

/** Строка позиции в форме заказа. */
interface OrderItemRow {
  product_id: number | '';
  count: number;
}

/** Данные формы заказа. */
interface OrderForm {
  id?: number;
  customer_id: number | '';
  warehouse_id: number | '';
  status: string;
  items: OrderItemRow[];
}

const route = useRoute();
const router = useRouter();
const isEdit = computed(() => route.name === 'order-edit');

const customers = ref<Customer[]>([]);
const warehouses = ref<Warehouse[]>([]);
const products = ref<Product[]>([]);
const order = reactive<OrderForm>({ customer_id: '', warehouse_id: '', status: 'active', items: [] });
const error = ref('');
const saving = ref(false);

function emptyRow(): OrderItemRow {
  return { product_id: '', count: 1 };
}

onMounted(async () => {
  try {
    const [cust, wh, prod] = await Promise.all([
      fetchCustomers(),
      fetchWarehouses(),
      fetchProducts(),
    ]);
    customers.value = cust;
    warehouses.value = wh;
    products.value = prod;
  } catch (e) {
    error.value = errorMessage(e);
  }

  if (isEdit.value) {
    await loadOrder();
  } else if (!order.items.length) {
    order.items.push(emptyRow());
  }
});

async function loadOrder(): Promise<void> {
  try {
    const d = await fetchOrder(String(route.params.id));
    order.id = d.id;
    order.customer_id = d.customer_id;
    order.warehouse_id = d.warehouse_id;
    order.status = d.status;
    order.items = d.items?.map((i) => ({ product_id: i.product_id, count: i.count })) ?? [];
  } catch (e) {
    error.value = errorMessage(e);
  }
}

function addRow(): void {
  order.items.push(emptyRow());
}
function removeRow(idx: number): void {
  order.items.splice(idx, 1);
  if (!order.items.length) order.items.push(emptyRow());
}

async function save(): Promise<void> {
  saving.value = true;
  error.value = '';
  const payload = {
    customer_id: Number(order.customer_id),
    warehouse_id: Number(order.warehouse_id),
    items: order.items
      .filter((i) => i.product_id !== '')
      .map((i) => ({ product_id: Number(i.product_id), count: i.count })),
  };
  try {
    if (isEdit.value) {
      await updateOrder(order.id!, payload);
    } else {
      await createOrder(payload);
    }
    router.push('/orders');
  } catch (e) {
    error.value = errorMessage(e);
  } finally {
    saving.value = false;
  }
}

async function changeStatus(action: 'complete' | 'cancel' | 'revive'): Promise<void> {
  error.value = '';
  try {
    await changeOrderStatus(order.id!, action);
    await loadOrder();
  } catch (e) {
    error.value = errorMessage(e);
  }
}

function rowSum(row: OrderItemRow): number {
  const p = products.value.find((x) => x.id === row.product_id);
  return p ? p.price * row.count : 0;
}
const total = computed(() => order.items.reduce((s, i) => s + rowSum(i), 0));

function statusClass(s: string): string {
  const base = 'inline-flex px-2 py-0.5 rounded-full text-xs font-semibold ';
  if (s === 'completed') return base + 'bg-green-100 text-green-700';
  if (s === 'canceled') return base + 'bg-red-100 text-red-700';
  return base + 'bg-blue-100 text-blue-700';
}
</script>

<template>
  <div>
    <RouterLink to="/orders" class="mb-4 inline-flex items-center rounded-md bg-gray-500 px-3 py-1.5 text-sm font-medium text-white hover:opacity-90">← Назад к заказам</RouterLink>

    <div v-if="error" class="mb-3 rounded-md bg-red-100 p-2.5 text-sm text-red-700">{{ error }}</div>

    <div class="mb-4 rounded-lg bg-white p-5 shadow-sm">
      <h3 class="text-lg font-semibold">{{ isEdit ? `Заказ #${order.id}` : 'Новый заказ' }}</h3>

      <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="mb-3">
          <label class="mb-1 block text-[13px] font-semibold">Клиент</label>
          <select v-model="order.customer_id" class="w-full rounded-md border border-gray-300 px-2.5 py-2 text-sm">
            <option value="">— Выберите клиента —</option>
            <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="mb-1 block text-[13px] font-semibold">Склад отгрузки</label>
          <select v-model="order.warehouse_id" class="w-full rounded-md border border-gray-300 px-2.5 py-2 text-sm">
            <option value="">— Выберите склад —</option>
            <option v-for="w in warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
          </select>
        </div>
      </div>
    </div>

    <div class="mb-4 rounded-lg bg-white p-5 shadow-sm">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">Позиции</h3>
        <button class="inline-flex items-center rounded-md bg-gray-500 px-3 py-1.5 text-sm font-medium text-white hover:opacity-90" @click="addRow" type="button">+ Добавить</button>
      </div>

      <table class="mt-3 w-full border-collapse bg-white text-sm">
        <thead>
          <tr>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold">Товар</th>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold">Цена</th>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold">Кол-во</th>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold">Сумма</th>
            <th class="border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left font-semibold"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(row, i) in order.items" :key="i">
            <td class="border-b border-gray-200 px-3 py-2.5 text-left">
              <select v-model="row.product_id" class="w-full rounded-md border border-gray-300 px-2.5 py-2 text-sm">
                <option value="">— Товар —</option>
                <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
              </select>
            </td>
            <td class="border-b border-gray-200 px-3 py-2.5 text-left">{{ (products.find(p => p.id === row.product_id)?.price ?? 0) }} ₽</td>
            <td class="border-b border-gray-200 px-3 py-2.5 text-left"><input type="number" min="1" v-model.number="row.count" class="w-20 rounded-md border border-gray-300 px-2.5 py-2 text-sm" /></td>
            <td class="border-b border-gray-200 px-3 py-2.5 text-left">{{ rowSum(row) }} ₽</td>
            <td class="border-b border-gray-200 px-3 py-2.5 text-left"><button class="inline-flex items-center rounded-md bg-red-600 px-3 py-1 text-sm font-medium text-white hover:opacity-90" type="button" @click="removeRow(i)">Удалить</button></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th class="border-t border-gray-200 px-3 py-2.5 text-left font-semibold" colspan="3">Итого</th>
            <th class="border-t border-gray-200 px-3 py-2.5 text-left font-semibold">{{ total }} ₽</th>
            <th class="border-t border-gray-200 px-3 py-2.5"></th>
          </tr>
        </tfoot>
      </table>
      <div class="mt-4">
        <button class="inline-flex items-center rounded-md bg-blue-600 px-3.5 py-1.5 text-sm font-medium text-white hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed" @click="save" :disabled="saving">
          {{ saving ? 'Сохранение...' : (isEdit ? 'Сохранить изменения' : 'Создать заказ') }}
        </button>
      </div>
    </div>

    <div v-if="isEdit" class="rounded-lg bg-white p-5 shadow-sm">
      <h3 class="mb-3 text-lg font-semibold">Действия со статусом</h3>
      <div class="flex gap-2.5 items-center">
        <button class="inline-flex items-center rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed" @click="changeStatus('complete')" :disabled="order.status !== 'active'">Завершить</button>
        <button class="inline-flex items-center rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed" @click="changeStatus('cancel')" :disabled="order.status !== 'active'">Отменить</button>
        <button class="inline-flex items-center rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed" @click="changeStatus('revive')" :disabled="order.status !== 'canceled'">Возобновить</button>
        <span :class="statusClass(order.status)">{{ order.status }}</span>
      </div>
    </div>
  </div>
</template>