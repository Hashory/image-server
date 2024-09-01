FROM php:8.1-apache

# Enable mod_headers
RUN a2enmod headers

# Enable mod_rewrite
RUN a2enmod rewrite

# Install driver for MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql
