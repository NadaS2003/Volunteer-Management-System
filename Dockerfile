FROM php:8.2-apache

# تثبيت الإضافات المطلوبة
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# تفعيل rewrite
RUN a2enmod rewrite

# تثبيت Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# نسخ المشروع
WORKDIR /var/www/html
COPY . .

# صلاحيات
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# تثبيت dependencies
RUN composer install --no-dev --optimize-autoloader


# فتح البورت
EXPOSE 80

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# تشغيل Apache
CMD ["apache2-foreground"]
