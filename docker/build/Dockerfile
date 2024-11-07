FROM debian:stable-slim AS dependencies
RUN export DEBIAN_FRONTEND=noninteractive \
    && apt-get -qy update \
    && apt-get -qy upgrade
RUN apt-get install -qy --no-install-recommends lsb-release apt-transport-https ca-certificates inetutils-ping wget \
    && wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
    && echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" |tee /etc/apt/sources.list.d/php.list \
    && apt-get -qy update \
    && apt-get -qy upgrade \
    && apt-get install -qy --no-install-recommends apache2 bind9 cron curl php8.3-fpm php8.3-gmp php8.3-mbstring php8.3-opcache php8.3-redis php8.3-xml redis-server \
    && apt-get install -qy --no-install-recommends php8.3-curl php8.3-dev unzip
RUN sed -i 's/;opcache.memory_consumption=128/opcache.memory_consumption=32/g' /etc/php/8.3/fpm/php.ini
RUN rm /var/www/html/index.html
RUN a2enmod expires headers proxy_fcgi remoteip rewrite setenvif \
    && a2enconf php8.3-fpm

FROM golang:1 AS geoipupdate-dependency
RUN env GO111MODULE=on go install github.com/maxmind/geoipupdate/v4/cmd/geoipupdate@latest

FROM composer:2 AS build-dependencies
WORKDIR /build
COPY . /build
RUN composer prod

FROM node:lts AS build-html
WORKDIR /build
COPY . /build
RUN ./docker/build/minify.sh

FROM dependencies AS common
ENV ASN_LINK false
ENV DATABASE_AUTO_UPDATE false
ENV DATABASE_DIR /var/databases
ENV DNS_CACHE false
ENV HOST_AUTO ""
ENV HOST_IPV4 ""
ENV HOST_IPV6 ""
ENV MAP_LINK false
ENV MAXMIND_ACCOUNT_ID ""
ENV MAXMIND_LICENSE_KEY ""
ENV RATE_LIMIT ""
ENV RATE_LIMIT_INTERVAL 1
ENV SHOW_ABOUT false
ENV SHOW_FAQ false
ENV SHOW_SUPPORT false
COPY ./docker/resources /
RUN a2enconf environment
COPY --from=geoipupdate-dependency /go/bin/geoipupdate /usr/bin/geoipupdate
WORKDIR /var/www
ENTRYPOINT [ "/entrypoint.sh" ]
STOPSIGNAL SIGWINCH
EXPOSE 80

FROM common AS dev
ENV MODE dev
WORKDIR /tmp
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

FROM common AS prod
ENV MODE prod
HEALTHCHECK --interval=30s --timeout=30s --start-period=30s --retries=3 CMD [ "curl", "-sf", "http://localhost/ip.txt" ]
COPY --chown=www-data:www-data --from=build-dependencies /build/app /var/www/app
COPY --chown=www-data:www-data --from=build-dependencies /build/vendor /var/www/vendor
COPY --chown=www-data:www-data --from=build-html /build/app/src/Renderer/Templates /var/www/app/src/Renderer/Templates
COPY --chown=www-data:www-data --from=build-html /build/html /var/www/html
RUN apt-get -qy autoremove --purge php8.3-curl php8.3-dev unzip \
    && apt-get clean \
    && rm -r /var/cache/apt/archives/* /var/lib/apt/lists/*
