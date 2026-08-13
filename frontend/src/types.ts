/**
 * Общие типы данных, используемые компонентами фронтенда.
 * Структуры повторяют формат ответов REST-API (v1).
 */

/** Метаинформация пагинации из ответа API. */
export interface Meta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

/** Ответ API со списком данных и пагинацией. */
export interface ApiList<T> {
  data: T[];
  meta: Meta;
}

/** Склад. */
export interface Warehouse {
  id: number;
  name: string;
}

/** Товар. */
export interface Product {
  id: number;
  name: string;
  price: number;
}

/** Клиент. */
export interface Customer {
  id: number;
  name: string;
  phone: string | null;
  email: string | null;
  created_at: string;
}

/** Позиция заказа. */
export interface OrderItem {
  id?: number;
  product_id: number;
  count: number;
  product?: Product;
}

/** Заказ. */
export interface Order {
  id: number;
  customer_id: number;
  warehouse_id: number;
  status: string;
  completed_at: string | null;
  created_at: string;
  customer?: Customer;
  warehouse?: Warehouse;
  items?: OrderItem[];
}

/** Запись истории движения товара. */
export interface Movement {
  id: number;
  doc_type: string;
  doc_id: number;
  product_id: number;
  warehouse_id: number;
  quantity: number;
  created_at: string;
  product?: Product;
  warehouse?: Warehouse;
}
