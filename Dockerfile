# Gunakan PHP 8.2 dengan Apache
FROM php:8.2-apache

# Aktifkan modul Apache
RUN a2enmod rewrite

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql zip

# Copy project ke container
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate key
RUN php artisan key:generate

# Set permission
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Command ketika start
CMD ["apache2-foreground"]
