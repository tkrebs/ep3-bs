FROM php:8.2-apache

# Install dependencies and intl extension
RUN apt-get update && apt-get install -y \
    libicu-dev \
    && docker-php-ext-install intl mysqli pdo pdo_mysql

RUN apt-get update && apt-get install -y default-mysql-client

# Enable mod_rewrite
RUN a2enmod rewrite

# Set Apache DocumentRoot to /var/www/html/public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Update Apache config to use the new document root
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}/../!g' /etc/apache2/apache2.conf


# Copy all project files to the container
COPY . /var/www/html/

# Overwrite config file: rename local-local-php to local.php
COPY ./config/autoload/local-local.php /var/www/html/config/autoload/local.php
RUN rm -f /var/www/html/config/autoload/local-local.php
RUN rm -f /var/www/html/config/autoload/local.php.dist

# Set working directory
WORKDIR /var/www/html
