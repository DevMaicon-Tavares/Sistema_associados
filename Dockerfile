FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    libpng-dev \
    libxml2-dev \
    libsqlite3-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo_sqlite mbstring bcmath intl xml zip \
    && a2enmod rewrite headers \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# Install PHP dependencies first to leverage build cache
COPY composer.json composer.lock ./
# Install PHP dependencies without running Composer scripts (artisan not present yet)
RUN composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction --no-scripts

# Install JS dependencies and build assets
COPY package.json package-lock.json vite.config.js ./
COPY resources ./resources
RUN npm install && npm run build

# Copy application files
COPY . .

# Now that the application files (including artisan) are present, run Composer scripts
RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi || true

# Set permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Configure Apache to use the public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/htdocs!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80
CMD ["apache2-foreground"]
