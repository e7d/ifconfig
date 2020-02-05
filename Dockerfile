FROM composer AS build
WORKDIR /build
COPY . /build/
RUN composer install \
    && composer dump-autoload

FROM node AS build-html
WORKDIR /build
COPY . /build/
RUN npx html-minifier --html5 --collapse-whitespace --remove-tag-whitespace --remove-comments --remove-optional-tags \
    --remove-redundant-attributes --remove-script-type-attributes --remove-tag-whitespace --use-short-doctype \
    --minify-css true --minify-js true -o app/src/Renderer/Templates/index.phtml -- app/src/Renderer/Templates/index.phtml

FROM alpine AS databases
ARG MAXMIND_LICENSE_KEY
WORKDIR /data
RUN apk add -U wget \
    && wget -qO- "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-ASN&date=20200121&license_key=$MAXMIND_LICENSE_KEY&suffix=tar.gz" | tar xz \
    && mv GeoLite2-ASN_*/GeoLite2-ASN.mmdb . \
    && wget -qO- "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&date=20200121&license_key=$MAXMIND_LICENSE_KEY&suffix=tar.gz" | tar xz \
    && mv GeoLite2-City_*/GeoLite2-City.mmdb .

FROM php:7-apache
COPY --from=build /build/app /var/www/app
COPY --from=build /build/vendor /var/www/vendor
COPY --from=build-html /build/app/src/Renderer/Templates/index.phtml /var/www/app/src/Renderer/Templates/index.phtml
COPY --from=databases /data/GeoLite2-ASN.mmdb /var/www/app/src/Reader/Databases/
COPY --from=databases /data/GeoLite2-City.mmdb /var/www/app/src/Reader/Databases/
COPY html /var/www/html
RUN docker-php-ext-install opcache \
    && a2enmod headers rewrite
EXPOSE 80
