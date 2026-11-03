FROM php:8.3-cli-alpine

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    mariadb-connector-c-dev \
    ca-certificates

RUN docker-php-ext-install pdo pdo_mysql mbstring

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy project files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Ensure permissions
RUN chmod -R 777 storage bootstrap/cache

# Render injects PORT env variable (default 10000)
ENV PORT=10000
EXPOSE 10000

CMD php artisan config:clear && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=${PORT}
