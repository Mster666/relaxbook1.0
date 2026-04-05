FROM node:20-alpine AS node_builder
WORKDIR /app
COPY package.json package-lock.json* ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi
COPY resources ./resources
COPY public ./public
COPY vite.config.* tailwind.config.* postcss.config.* ./
RUN npm run build

FROM php:8.3-fpm AS app
WORKDIR /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        git \
        gettext-base \
        nginx \
        supervisor \
        unzip \
        libzip-dev \
        libicu-dev \
    && docker-php-ext-install intl zip pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY . .
COPY --from=node_builder /app/public/build ./public/build

RUN set -eux; \
    mkdir -p public/js; \
    curl -fsSL https://unpkg.com/alpinejs@3.14.9/dist/cdn.min.js -o public/js/alpine.min.js

RUN set -eux; \
    mkdir -p \
        storage/app/public \
        storage/framework/views \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/logs \
        bootstrap/cache; \
    rm -rf public/storage; \
    ln -s ../storage/app/public public/storage; \
    chmod -R 0777 storage bootstrap/cache

COPY docker/nginx.conf.template /etc/nginx/templates/default.conf.template
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-relaxbook.ini

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV VIEW_COMPILED_PATH=/app/storage/framework/views

RUN chmod +x /app/entrypoint.sh \
    && rm -f /etc/nginx/sites-enabled/default /etc/nginx/conf.d/default.conf || true

CMD ["/app/entrypoint.sh"]
