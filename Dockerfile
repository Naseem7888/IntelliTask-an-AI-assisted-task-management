# Production-ready Dockerfile for Laravel (PHP-FPM + Nginx + Node build)
# Multi-stage build

# 1) Build PHP dependencies with Composer
FROM php:8.2-fpm-alpine AS php-deps
RUN apk add --no-cache git zip unzip icu-dev oniguruma-dev libpng-dev libzip-dev libxml2-dev freetype-dev libjpeg-turbo-dev postgresql-dev
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl mbstring pdo pdo_pgsql gd zip opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

# 2) Build frontend with Node
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci --no-audit --no-fund
COPY resources ./resources
COPY vite.config.js tailwind.config.js postcss.config.js ./
RUN npm run build

# 3) Runtime image with Nginx + PHP-FPM
FROM nginx:1.27-alpine AS runtime

# Install PHP-FPM via separate container and copy from php-deps stage
# We'll run PHP-FPM in a sidecar-like fashion using s6 overlay is overkill; instead use supervisord
RUN apk add --no-cache php82 php82-fpm php82-opcache php82-ctype php82-mbstring php82-pdo php82-pdo_pgsql php82-gd php82-zip php82-intl php82-session php82-tokenizer php82-dom php82-fileinfo php82-openssl php82-simplexml php82-xml php82-xmlwriter php82-curl supervisor
# Make php CLI available as `php` for convenience
RUN ln -s /usr/bin/php82 /usr/bin/php || true

# Configure Nginx
COPY ./deploy/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
COPY ./deploy/php-fpm.conf /etc/php82/php-fpm.d/www.conf

# Configure Supervisor to run both services
COPY ./deploy/supervisord.conf /etc/supervisord.conf
COPY ./deploy/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html

# Copy app code
COPY . /var/www/html
RUN find /var/www/html -type f -name "*.sh" -exec sed -i 's/\r$//' {} + && \
    chmod +x /usr/local/bin/entrypoint.sh

# Copy vendor from php-deps
COPY --from=php-deps /app/vendor /var/www/html/vendor

# Copy built assets
COPY --from=assets /app/public/build /var/www/html/public/build

# Permissions
RUN adduser -D -H -u 1000 appuser \
    && chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr

EXPOSE 8080
CMD ["/usr/local/bin/entrypoint.sh"]
