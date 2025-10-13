FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    bash git unzip icu-dev libzip-dev oniguruma-dev libpng-dev libjpeg-turbo-dev freetype-dev tzdata

# PHP extensions commonly needed by Symfony
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) pdo_mysql intl zip gd opcache

# Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/tmp/composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Small helper: if the app dir is empty, create a new Symfony skeleton on container start
COPY init.sh /usr/local/bin/init.sh
RUN chmod +x /usr/local/bin/init.sh

# Tune PHP-FPM a bit (optional)
RUN { \
      echo "opcache.enable=1"; \
      echo "opcache.validate_timestamps=1"; \
      echo "opcache.memory_consumption=128"; \
      echo "opcache.max_accelerated_files=10000"; \
    } > /usr/local/etc/php/conf.d/symfony.ini

CMD ["/usr/local/bin/init.sh"]
