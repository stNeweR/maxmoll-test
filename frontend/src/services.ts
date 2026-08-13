import api, { errorMessage } from './api';
import type { ApiList, Customer, Movement, Order, Product, Warehouse } from './types';

export { errorMessage };

/** Общие параметры запроса (пагинация + произвольные фильтры). */
export interface ListQuery {
  page?: number;
  per_page?: number;
  [key: string]: string | number | undefined;
}

/** Отбрасывает пустые/неопределённые параметры перед отправкой. */
function clean(params: Record<string, unknown>): Record<string, string | number> {
  return Object.fromEntries(
    Object.entries(params).filter(([, v]) => v !== '' && v !== undefined),
  ) as Record<string, string | number>;
}

/** Получение списка складов. */
export async function fetchWarehouses(): Promise<Warehouse[]> {
  const res = await api.get<ApiList<Warehouse>>('/warehouses');
  return res.data.data;
}

/** Получение списка товаров. */
export async function fetchProducts(): Promise<Product[]> {
  const res = await api.get<ApiList<Product>>('/products');
  return res.data.data;
}

/** Получение списка клиентов (для формы заказа). */
export async function fetchCustomers(): Promise<Customer[]> {
  const res = await api.get<ApiList<Customer>>('/customers', { params: { per_page: 100 } });
  return res.data.data;
}

/** Получение списка заказов с фильтрацией и пагинацией. */
export async function fetchOrders(page: number, status: string, per_page: number): Promise<ApiList<Order>> {
  const res = await api.get<ApiList<Order>>('/orders', { params: clean({ page, status, per_page }) });
  return res.data;
}

/** Получение одного заказа. */
export async function fetchOrder(id: string | number): Promise<Order> {
  const res = await api.get<{ data: Order }>(`/orders/${id}`);
  return res.data.data;
}

/** Данные для создания/обновления заказа. */
export interface OrderPayload {
  customer_id: number;
  warehouse_id: number;
  items: { product_id: number; count: number }[];
}

/** Создание нового заказа. */
export async function createOrder(payload: OrderPayload): Promise<Order> {
  const res = await api.post<{ data: Order }>('/orders', payload);
  return res.data.data;
}

/** Обновление существующего заказа. */
export async function updateOrder(id: number, payload: OrderPayload): Promise<Order> {
  const res = await api.put<{ data: Order }>(`/orders/${id}`, payload);
  return res.data.data;
}

/** Смена статуса заказа (завершить / отменить / возобновить). */
export async function changeOrderStatus(id: number, action: 'complete' | 'cancel' | 'revive'): Promise<Order> {
  const res = await api.post<{ data: Order }>(`/orders/${id}/${action}`);
  return res.data.data;
}

/** Фильтры истории движений. */
export interface MovementFilters {
  warehouse_id?: number;
  product_id?: number;
  doc_type?: string;
  date_from?: string;
  date_to?: string;
}

/** Получение истории движений с фильтрацией и пагинацией. */
export async function fetchMovements(
  page: number,
  per_page: number,
  filters: MovementFilters,
): Promise<ApiList<Movement>> {
  const res = await api.get<ApiList<Movement>>('/movements', { params: clean({ page, per_page, ...filters }) });
  return res.data;
}