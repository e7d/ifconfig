FROM composer AS build
WORKDIR /build
COPY . /build/
RUN composer install \
    && composer dump-autoload

FROM alpine AS databases
ARG MAXMIND_LICENSE_KEY
ENV MAXMIND_LICENSE_KEY $MAXMIND_LICENSE_KEY
WORKDIR /data
RUN echo MAXMIND_LICENSE_KEY: $MAXMIND_LICENSE_KEY \
    && apk add -U wget \
    && wget -qO- "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-ASN&date=20200121&license_key=$MAXMIND_LICENSE_KEY&suffix=tar.gz" | tar xz \
    && mv GeoLite2-ASN_*/GeoLite2-ASN.mmdb . \
    && wget -qO- "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&date=20200121&license_key=$MAXMIND_LICENSE_KEY&suffix=tar.gz" | tar xz \
    && mv GeoLite2-City_*/GeoLite2-City.mmdb .

FROM php:7-apache
COPY --from=build /build/app /var/www/app
COPY --from=databases /data/GeoLite2-ASN.mmdb /var/www/app/src/Reader/Databases
COPY --from=databases /data/GeoLite2-City.mmdb /var/www/app/src/Reader/Databases
COPY --from=build /build/vendor /var/www/vendor
COPY html /var/www/html
RUN docker-php-ext-install opcache \
    && ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load
EXPOSE 80
