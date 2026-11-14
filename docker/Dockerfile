# Imagen de producción: PHP-FPM 8.4 con las extensiones que usa el sistema.
# Etapa 1: compila los assets (Vite + Tailwind).
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN --mount=type=secret,id=corp_ca if [ -s /run/secrets/corp_ca ]; then NODE_EXTRA_CA_CERTS=/run/secrets/corp_ca npm ci; else npm ci; fi
COPY resources ./resources
COPY vite.config.js tailwind.config.js postcss.config.js ./
RUN npm run build

# Etapa 2: dependencias de Composer sin las de desarrollo.
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN --mount=type=secret,id=corp_ca if [ -s /run/secrets/corp_ca ]; then cp /run/secrets/corp_ca /usr/local/share/ca-certificates/corp-ca.crt && update-ca-certificates; fi \
    && composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

# Etapa 3: aplicación.
FROM php:8.4-fpm-alpine AS app
RUN --mount=type=secret,id=corp_ca if [ -s /run/secrets/corp_ca ]; then cp /run/secrets/corp_ca /usr/local/share/ca-certificates/corp-ca.crt && update-ca-certificates; fi \
    && apk add --no-cache postgresql-client libpq-dev icu-dev libzip-dev oniguruma-dev libxml2-dev $PHPIZE_DEPS \
    && docker-php-ext-install -j4 pdo_pgsql pgsql mbstring dom xml xmlwriter bcmath intl zip opcache

ARG PHPREDIS_VERSION=6.2.0
RUN curl -fsSL "https://codeload.github.com/phpredis/phpredis/tar.gz/refs/tags/${PHPREDIS_VERSION}" -o /tmp/redis.tgz \
    && echo "470333b27cbf9485d36b610b81300c06491a6575f22c6801a9cefc55285ed123  /tmp/redis.tgz" | sha256sum -c - \
    && mkdir /tmp/phpredis && tar -xzf /tmp/redis.tgz -C /tmp/phpredis --strip-components=1 \
    && cd /tmp/phpredis && phpize && ./configure --enable-redis && make -j4 && make install \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/phpredis /tmp/redis.tgz \
    && apk del $PHPIZE_DEPS
COPY docker/php.ini /usr/local/etc/php/conf.d/pipocas.ini

WORKDIR /var/www/html
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN mkdir -p storage/app/private storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs storage/backups bootstrap/cache \
    && composer dump-autoload --optimize --no-dev \
    && composer check-platform-reqs --no-dev \
    && php artisan storage:link \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod 700 storage/app/private storage/backups \
    && rm -f public/hot

USER www-data
EXPOSE 9000
CMD ["php-fpm"]

# Nginx recibe los estáticos del mismo build; no quedan ocultos bajo un volumen antiguo.
FROM nginx:1.27-alpine AS web
COPY --from=app /var/www/html/public /var/www/html/public
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
