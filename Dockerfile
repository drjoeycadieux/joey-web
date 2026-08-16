FROM php:8.3-apache

RUN docker-php-ext-install mysqli

RUN a2enmod rewrite

COPY api/ /var/www/html/api/

RUN chown -R www-data:www-data /var/www/html/api \
    && chmod -R 755 /var/www/html/api

# Apache listens on Render's expected port
RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' \
        /etc/apache2/sites-available/000-default.conf

EXPOSE 10000