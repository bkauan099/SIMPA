FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpq-dev curl unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql pgsql

RUN a2enmod rewrite

# Corrige o AllowOverride para o .htaccess funcionar
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Instala dependências PHP (PHPMailer etc.)
RUN cd /var/www/html && composer install --no-dev --optimize-autoloader

EXPOSE 80