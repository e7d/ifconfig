FROM php:7-apache
ENV RATE_LIMIT_INTERVAL 1
COPY ./docker /
RUN a2enmod expires headers rewrite
RUN docker-php-ext-install opcache
RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis
