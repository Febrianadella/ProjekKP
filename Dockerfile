FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction --ignore-platform-reqs

# Copy package files
COPY package.json package-lock.json ./

# Install npm dependencies
RUN npm ci

# Copy application files
COPY . .

# Build assets
RUN npm run build

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 8080

# Start command - removed db:seed because it uses Factory which needs dev dependencies
CMD php artisan config:clear && php artisan migrate --force && php artisan storage:link && php -S 0.0.0.0:${PORT:-8080} -t public
