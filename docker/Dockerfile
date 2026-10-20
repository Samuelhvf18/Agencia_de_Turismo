# Imagen de producción: PHP-FPM 8.4 con las extensiones que usa el sistema.
# Etapa 1: compila los assets (Vite + Tailwind).
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js tailwind.config.js postcss.config.js ./
RUN npm run build

# Etapa 2: dependencias de Composer sin las de desarrollo.
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

# Etapa 3: aplicación.
FROM php:8.4-fpm-alpine
RUN apk add --no-cache postgresql-client libpq-dev icu-dev libzip-dev oniguruma-dev $PHPIZE_DEPS \
    && docker-php-ext-install pdo_pgsql pgsql bcmath intl zip opcache \
    && pecl install redis && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS
COPY docker/php.ini /usr/local/etc/php/conf.d/pipocas.ini

WORKDIR /var/www/html
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --optimize --no-dev && rm -f public/hot

USER www-data
EXPOSE 9000
CMD ["php-fpm"]
