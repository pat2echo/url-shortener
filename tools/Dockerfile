FROM php:8.2-apache

# Install required dependencies
RUN apt-get update \
  && apt-get install -y \
                     wait-for-it \
                     unzip \
                     curl \
                     libfreetype6-dev \
                     libjpeg62-turbo-dev \
                     libpng-dev \
                     libzip-dev \
                     libcurl3-dev \
                     libicu-dev \
                     libwebp-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
  && docker-php-ext-configure intl \
  && docker-php-ext-install -j$(nproc) gd zip mysqli curl pdo_mysql intl \
  && docker-php-ext-enable gd zip mysqli curl

# Install PHP opcache for performance (PHP accelerator)
RUN docker-php-ext-install opcache

# Enable Apache mod_rewrite for Laravel
RUN a2enmod rewrite

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Verify Composer installation
RUN composer --version

# Copy Apache virtual host configuration
COPY ./laravel.conf /etc/apache2/sites-available/000-default.conf

# Add custom PHP configuration
ADD ./extra-php.ini /usr/local/etc/php/conf.d/nwp.ini

# Set proper permissions
# RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache