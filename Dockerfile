FROM php:8.2-apache

# Instalar mysqli
RUN docker-php-ext-install mysqli

# Copiar archivos
COPY . /var/www/html/

# Activar rewrite
RUN a2enmod rewrite