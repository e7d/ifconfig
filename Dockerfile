FROM composer AS build
WORKDIR /build
COPY . /build/
RUN composer install \
    && composer dump-autoload

FROM node AS build-html
WORKDIR /build
COPY . /build/
RUN npx html-minifier --html5 \
    --collapse-boolean-attributes \
    --collapse-inline-tag-whitespace \
    --collapse-whitespace \
    --decode-entities \
    --minify-css true \
    --minify-js true \
    --minify-urls \
    --remove-attribute-quotes \
    --remove-comments \
    --remove-empty-attributes \
    --remove-empty-elements \
    --remove-optional-tags \
    --remove-redundant-attributes \
    --remove-script-type-attributes \
    --remove-style-link-type-attributes \
    --remove-tag-whitespace \
    --sort-attributes \
    --sort-class-name \
    --use-short-doctype \
    -o app/src/Renderer/Templates/index.min.phtml -- app/src/Renderer/Templates/index.phtml \
    && npx uglifycss \
    html/style.css >html/style.min.css \
    && CKSUM=$(cksum html/style.min.css | cut -d' ' -f 1) \
    && mv html/style.min.css html/style.${CKSUM}.css \
    && sed -i "s/style\.css/style.${CKSUM}.css/g" app/src/Renderer/Templates/index.min.phtml

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
COPY html /var/www/html
COPY --from=build /build/app /var/www/app
COPY --from=build /build/vendor /var/www/vendor
COPY --from=build-html /build/app/src/Renderer/Templates/index.min.phtml /var/www/app/src/Renderer/Templates/index.phtml
COPY --from=build-html /build/html/style.*.css /var/www/html/
COPY --from=databases /data/GeoLite2-ASN.mmdb /var/www/app/src/Reader/Databases/
COPY --from=databases /data/GeoLite2-City.mmdb /var/www/app/src/Reader/Databases/
EXPOSE 80
