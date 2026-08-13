# PHP-FPM контейнер для Laravel приложения
FROM php:8.4-fpm-alpine

# Устанавливаем системные зависимости и расширения PHP, необходимые для Laravel/MySQL
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && apk add --no-cache icu-dev libzip-dev oniguruma-dev gettext-dev zlib-dev \
    && docker-php-ext-install pdo_mysql intl pcntl bcmath zip opcache \
    && docker-php-ext-enable pdo_mysql intl pcntl bcmath zip opcache \
    && apk del .build-deps

# Устанавливаем Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Рабочая директория приложения
WORKDIR /var/www

# Конфигурация PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini

EXPOSE 9000

CMD ["php-fpm"]