FROM php:8.2-apache

# تثبيت الإضافات المطلوبة وأدوات النظام
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# تفعيل Apache Rewrite Module
RUN a2enmod rewrite

# تثبيت Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# نسخ ملفات الاعتماديات أولاً
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# نسخ باقي ملفات المشروع
COPY . .

RUN composer dump-autoload --optimize

# إعداد الصلاحيات
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# نسخ ملف الـ entrypoint وإعطاؤه صلاحيات التنفيذ
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

# استخدام السکرپت لتشغيل المايجريشن ثم أباتشي
CMD ["entrypoint.sh"]
