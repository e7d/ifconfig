FROM composer AS build-dependencies
WORKDIR /build
COPY . /build/
RUN composer setup

FROM node AS build-html
WORKDIR /build
COPY . /build/
RUN ./minify.sh

FROM alpine AS databases
ARG MAXMIND_LICENSE_KEY
WORKDIR /data
RUN apk add -U wget \
    && wget -qO- "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-ASN&license_key=$MAXMIND_LICENSE_KEY&suffix=tar.gz" | tar xz \
    && mv GeoLite2-ASN_*/GeoLite2-ASN.mmdb . \
    && wget -qO- "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=$MAXMIND_LICENSE_KEY&suffix=tar.gz" | tar xz \
    && mv GeoLite2-City_*/GeoLite2-City.mmdb .

FROM php:7-apache
RUN docker-php-ext-install opcache \
    && a2enmod headers expires rewrite
COPY docker/expires.conf /etc/apache2/conf-enabled/expires.conf
COPY --from=build-dependencies /build/app /var/www/app
COPY --from=build-dependencies /build/vendor /var/www/vendor
COPY --from=build-html /build/app/src/Renderer/Templates /var/www/app/src/Renderer/Templates
COPY --from=build-html /build/html /var/www/html
COPY --from=databases /data/GeoLite2-ASN.mmdb /var/www/app/src/Reader/Databases/
COPY --from=databases /data/GeoLite2-City.mmdb /var/www/app/src/Reader/Databases/
EXPOSE 80
