FROM php:7.3.10-fpm-alpine3.10

RUN apk add --no-cache $PHPIZE_DEPS tzdata
RUN echo "Europe/Kiev" > /etc/timezone
RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql && docker-php-ext-install pdo_mysql
RUN pecl install -o -f redis && rm -rf /tmp/pear && docker-php-ext-enable redis
