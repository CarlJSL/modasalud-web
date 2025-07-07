FROM php:8.2-apache

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    unzip \
    zip \
    git \
    curl \
    && docker-php-ext-install pdo_pgsql pgsql zip gd

# Habilitar mod_rewrite para .htaccess
RUN a2enmod rewrite

# Cambiar DocumentRoot a /var/www/html/app
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/app|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|<Directory /var/www/html>|<Directory /var/www/html/app>|g' /etc/apache2/apache2.conf

# Instalar Composer desde contenedor oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

EXPOSE 80
