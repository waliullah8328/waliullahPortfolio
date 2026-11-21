# Vercel container image (FrankenPHP). Vercel builds this on every push to GitHub.
# Config lives in Vercel env vars (APP_KEY, DATABASE_URL from Neon, ...), never in the image.

# ---- PHP base: PHP 8.4 + the extensions this app needs ----------------------------
FROM dunglas/frankenphp:1-php8.4-bookworm AS base
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && install-php-extensions intl zip pdo_pgsql opcache
WORKDIR /app

# ---- Composer dependencies --------------------------------------------------------
FROM base AS vendor
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --no-progress
COPY . .
RUN mkdir -p bootstrap/cache storage/logs storage/framework/cache/data storage/framework/sessions storage/framework/views \
    && composer dump-autoload --no-dev --classmap-authoritative --no-interaction

# ---- Front-end assets (Vite + Tailwind; Tailwind scans app/, resources/ and vendor views) ----
FROM node:22-bookworm-slim AS assets
WORKDIR /app
COPY package.json package-lock.json .npmrc ./
RUN npm ci --no-fund
COPY --from=vendor /app /app
RUN npm run build

# ---- Runtime ----------------------------------------------------------------------
FROM base AS runtime
COPY --from=vendor --chown=www-data:www-data /app /app
COPY --from=assets --chown=www-data:www-data /app/public/build /app/public/build
COPY --chown=www-data:www-data Caddyfile /etc/frankenphp/Caddyfile

# The container filesystem is not durable: logs go to stderr, uploads are turned off
# (paste Google Drive / Dropbox links in the admin instead), sessions + cache live in Postgres.
ENV PORT=80 \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=warning \
    DB_CONNECTION=pgsql \
    SESSION_DRIVER=database \
    CACHE_STORE=database \
    QUEUE_CONNECTION=sync \
    MAIL_MAILER=log \
    MEDIA_UPLOADS=false

# Compile Blade views and events at build time (config is NOT cached: env vars arrive at runtime).
RUN php artisan view:cache && php artisan event:cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp \
    && mkdir -p /config/caddy /data/caddy \
    && chown -R www-data:www-data /config/caddy /data/caddy

USER www-data
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
