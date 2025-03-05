FROM php:8.2-apache

RUN apt-get update \
  && apt-get install -y \
                     wait-for-it \
                     unzip \
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

RUN a2enmod rewrite

ADD ./extra-php.ini /usr/local/etc/php/conf.d/nwp.ini
