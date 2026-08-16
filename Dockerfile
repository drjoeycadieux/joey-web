FROM php:8.3-apache

# Install mysqli
RUN docker-php-ext-install mysqli

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy the API into Apache's web root
COPY api/ /var/www/html/api/

# Make sure Apache can read the files
RUN chown -R www-data:www-data /var/www/html/api \
    && chmod -R 755 /var/www/html/api

# Render uses the PORT environment variable.
# Apache listens on 80 inside the container.
RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && sed -i 's/:80>/:10000>/g' /etc/apache2/sites-available/000-default.conf

EXPOSE 10000