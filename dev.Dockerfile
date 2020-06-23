FROM php:7-apache
ENV DATABASE_DIR /var/www/app/src/Reader/Databases/
ENV RATE_LIMIT_INTERVAL 1
COPY ./docker /
RUN a2enmod expires headers rewrite
RUN docker-php-ext-install opcache
RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis
ENTRYPOINT [ "/var/www/docker/entrypoint.sh" ]
CMD [ "apache2-foreground" ]
