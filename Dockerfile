FROM composer AS build
WORKDIR /build
COPY . /build/
RUN composer install \
    && composer dump-autoload

FROM node AS build-html
WORKDIR /build
COPY . /build/
RUN npx html-minifier --html5 --collapse-boolean-attributes --collapse-inline-tag-whitespace --collapse-whitespace --decode-entities --minify-css true --minify-js true --minify-urls --remove-attribute-quotes --remove-comments --remove-empty-attributes --remove-empty-elements --remove-optional-tags --remove-redundant-attributes --remove-script-type-attributes --remove-style-link-type-attributes --remove-tag-whitespace --sort-attributes --sort-class-name --use-short-doctype -o app/src/Renderer/Templates/index.min.phtml -- app/src/Renderer/Templates/index.phtml \
    && mv app/src/Renderer/Templates/index.min.phtml app/src/Renderer/Templates/index.phtml \
    && npx html-minifier --html5 --collapse-boolean-attributes --collapse-inline-tag-whitespace --collapse-whitespace --decode-entities --minify-css true --minify-js true --minify-urls --remove-attribute-quotes --remove-comments --remove-empty-attributes --remove-empty-elements --remove-optional-tags --remove-redundant-attributes --remove-script-type-attributes --remove-style-link-type-attributes --remove-tag-whitespace --sort-attributes --sort-class-name --use-short-doctype -o app/src/Renderer/Templates/about.min.phtml -- app/src/Renderer/Templates/about.phtml \
    && mv app/src/Renderer/Templates/about.min.phtml app/src/Renderer/Templates/about.phtml \
    && npx html-minifier --html5 --collapse-boolean-attributes --collapse-inline-tag-whitespace --collapse-whitespace --decode-entities --minify-css true --minify-js true --minify-urls --remove-attribute-quotes --remove-comments --remove-empty-attributes --remove-empty-elements --remove-optional-tags --remove-redundant-attributes --remove-script-type-attributes --remove-style-link-type-attributes --remove-tag-whitespace --sort-attributes --sort-class-name --use-short-doctype -o app/src/Renderer/Templates/info.min.phtml -- app/src/Renderer/Templates/info.phtml \
    && mv app/src/Renderer/Templates/info.min.phtml app/src/Renderer/Templates/info.phtml \
    && npx uglifycss html/css/style.css >html/css/style.min.css \
    && CHECKSUM=$(cksum html/css/style.min.css | cut -d' ' -f 1) \
    && mv html/css/style.min.css html/css/style.${CHECKSUM}.css \
    && sed -i "s/style\.css/style.${CHECKSUM}.css/g" app/src/Renderer/Templates/index.phtml \
    && rm html/css/style.css \
    && npx uglifycss html/css/about.css >html/css/about.min.css \
    && CHECKSUM=$(cksum html/css/about.min.css | cut -d' ' -f 1) \
    && mv html/css/about.min.css html/css/about.${CHECKSUM}.css \
    && sed -i "s/about\.css/about.${CHECKSUM}.css/g" app/src/Renderer/Templates/about.phtml \
    && rm html/css/about.css \
    && npx uglifycss html/css/info.css >html/css/info.min.css \
    && CHECKSUM=$(cksum html/css/info.min.css | cut -d' ' -f 1) \
    && mv html/css/info.min.css html/css/info.${CHECKSUM}.css \
    && sed -i "s/info\.css/info.${CHECKSUM}.css/g" app/src/Renderer/Templates/info.phtml \
    && rm html/css/info.css \
    && npx uglify-es html/js/query.js >html/js/query.min.js \
    && CHECKSUM=$(cksum html/js/query.min.js | cut -d' ' -f 1) \
    && mv html/js/query.min.js html/js/query.${CHECKSUM}.js \
    && sed -i "s/query\.js/query.${CHECKSUM}.js/g" app/src/Renderer/Templates/about.phtml \
    && rm html/js/query.js

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
COPY --from=build /build/app /var/www/app
COPY --from=build /build/vendor /var/www/vendor
COPY --from=build-html /build/app/src/Renderer/Templates /var/www/app/src/Renderer/Templates
COPY --from=build-html /build/html /var/www/html
COPY --from=databases /data/GeoLite2-ASN.mmdb /var/www/app/src/Reader/Databases/
COPY --from=databases /data/GeoLite2-City.mmdb /var/www/app/src/Reader/Databases/
EXPOSE 80
