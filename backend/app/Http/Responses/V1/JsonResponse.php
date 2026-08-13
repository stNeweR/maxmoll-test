<?php

namespace App\Http\Responses\V1;

use Illuminate\Http\JsonResponse as BaseJsonResponse;

/**
 * Версионированный JsonResponse для API v1.
 *
 * Заменяет использование хелпера response()->json(): контроллеры возвращают
 * экземпляры этого класса (наследника Laravel JsonResponse).
 */
class JsonResponse extends BaseJsonResponse
{
    /**
     * Ответ с произвольным телом запроса.
     *
     * @param array $payload полное тело JSON-ответа
     */
    public static function payload(array $payload, int $status = 200): static
    {
        return new static($payload, $status);
    }

    /**
     * Успешный ответ с данными (оборачивает их в ключ "data").
     */
    public static function success(mixed $data, int $status = 200): static
    {
        return new static(['data' => $data], $status);
    }

    /**
     * Ответ со списком данных и метаинформацией пагинации.
     */
    public static function list(array $data, array $meta, int $status = 200): static
    {
        return new static(['data' => $data, 'meta' => $meta], $status);
    }

    /**
     * Ответ с ошибкой.
     */
    public static function error(string $message, int $status = 422): static
    {
        return new static(['message' => $message], $status);
    }
}