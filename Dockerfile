FROM php:7.1-apache

# install
RUN apt-get update && apt-get install -y \
    git \
    zlib1g-dev \
    libmcrypt-dev \
    libpcre3-dev \
    mariadb-client \
    libmemcached-dev \
  && echo no | pecl install memcached \
  && docker-php-source extract \
  && docker-php-ext-install zip mcrypt \
  && git clone https://github.com/php-memcached-dev/php-memcached /usr/src/php/ext/memcached/ \
  && docker-php-ext-install pdo pdo_mysql memcached \
  && docker-php-source delete

# Enable mod rewrite
RUN a2enmod actions rewrite headers expires

#PHP config
ADD docker/php.ini /usr/local/etc/php

# Composer
RUN php -r "readfile('https://getcomposer.org/installer');" | php \
  && mv composer.phar /usr/local/bin/composer

# Add code
ADD . /var/www/html

# Launch composer for autoloader and scripts
RUN composer install --no-dev

RUN composer dump-autoload -o -a