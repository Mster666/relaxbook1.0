FROM node:20-alpine AS node_builder
WORKDIR /app
COPY package.json package-lock.json* ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi
COPY resources ./resources
COPY public ./public
COPY vite.config.* tailwind.config.* postcss.config.* ./
RUN npm run build

FROM php:8.3-cli AS app
WORKDIR /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
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

RUN php -r "is_dir('storage') || mkdir('storage', 0777, true);" \
    && php -r "is_dir('bootstrap/cache') || mkdir('bootstrap/cache', 0777, true);" \
    && php -r "is_link('public/storage') || (file_exists('public/storage') ?: symlink('../storage/app/public', 'public/storage'));"

ENV APP_ENV=production
ENV APP_DEBUG=false

CMD ["sh", "-lc", "php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
