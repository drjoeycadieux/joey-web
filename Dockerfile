FROM php:8.3-apache

RUN docker-php-ext-install mysqli

RUN a2enmod rewrite

COPY api/ /var/www/html/api/

RUN echo '<?php echo "joey-web PHP API is running"; ?>' > /var/www/html/index.php

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf

RUN sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' \
    /etc/apache2/sites-available/000-default.conf

EXPOSE 10000