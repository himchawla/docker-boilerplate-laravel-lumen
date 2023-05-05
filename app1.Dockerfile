FROM php:8.2-fpm-alpine

RUN apk add --no-cache linux-headers
RUN apk update
RUN apk add vim
RUN apk add  zip unzip git libzip-dev zlib-dev




RUN git clone https://github.com/phpredis/phpredis.git /usr/src/php/ext/redis

RUN docker-php-ext-install pdo_mysql redis
#
#RUN git clone https://github.com/php/php-src/blob/master/ext/sockets /usr/src/php/ext/sockets
#
RUN docker-php-ext-install sockets

#RUN CFLAGS="$CFLAGS -D_GNU_SOURCE" docker-php-ext-install sockets

#
RUN docker-php-ext-enable redis sockets pdo_mysql
#
COPY php.ini /usr/local/etc/php/
#
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
#
USER www-data
#
COPY --chown=www-data:www-data ./www/app1 /var/www/app1
#
WORKDIR /var/www/app1/public
#
#
#
RUN composer update && composer install
#
#
#
EXPOSE 9000
#
CMD ["php-fpm"]