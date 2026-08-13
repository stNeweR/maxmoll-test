import axios, { type AxiosError, type AxiosInstance } from 'axios';

// Клиент API. Базовый URL /api/v1 проксируется Vite на Laravel.
const api: AxiosInstance = axios.create({
  baseURL: '/api/v1',
  headers: { 'Content-Type': 'application/json' },
});

/** Тело ответа с ошибкой от бэкенда. */
interface ErrorBody {
  message?: string;
}

/**
 * Извлекает человекочитаемое сообщение об ошибке из ответа.
 */
export function errorMessage(error: unknown): string {
  const err = error as AxiosError<ErrorBody>;
  return err?.response?.data?.message || err.message || 'Неизвестная ошибка';
}

export default api;