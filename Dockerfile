FROM composer AS build
COPY app /app
WORKDIR /app
RUN ls -lah && composer install \
 && composer dump-autoload

FROM php:7-apache
COPY --from=build /app /var/www/app
COPY html /var/www/html
RUN ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load
EXPOSE 80
