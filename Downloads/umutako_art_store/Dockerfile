# ================================================
#  Dockerfile — Umutako Art Store
#  Uses Apache + PHP image
# ================================================

FROM php:8.2-apache

# Enable Apache mod_rewrite (useful for clean URLs later)
RUN a2enmod rewrite

# Copy all website files into Apache's web root
COPY . /var/www/html/

# Give Apache permission to read the files
RUN chown -R www-data:www-data /var/www/html/

# Install PHP mysqli extension (needed to connect to MySQL)
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Expose port 80 (standard HTTP)
EXPOSE 80
