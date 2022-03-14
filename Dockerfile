FROM debian:buster-slim AS dependencies
RUN export DEBIAN_FRONTEND=noninteractive \
    && apt-get -qy update \
    && apt-get -qy upgrade
RUN apt-get install -qy lsb-release apt-transport-https ca-certificates wget \
    && wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
    && echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" |tee /etc/apt/sources.list.d/php.list \
    && apt-get -qy update \
    && apt-get install -qy apache2 bind9 cron php8.1-dev php8.1-fpm php8.1-opcache php8.1-redis redis-server
RUN rm /var/www/html/index.html
RUN a2enmod expires headers proxy_fcgi rewrite setenvif \
    && a2enconf php8.1-fpm
RUN apt-get -qy autoremove --purge php8.1-dev \
    && apt-get clean \
    && rm -r /var/cache/apt/archives/* /var/lib/apt/lists/*

FROM golang AS geoipupdate-dependency
RUN env GO111MODULE=on go install github.com/maxmind/geoipupdate/v4/cmd/geoipupdate@latest

FROM composer AS build-dependencies
WORKDIR /build
COPY . /build/
RUN composer prod

FROM node AS build-html
WORKDIR /build
COPY . /build/
RUN ./minify.sh

FROM dependencies
ENV DATABASE_AUTO_UPDATE ""
ENV DATABASE_DIR /var/databases
ENV DNS_CACHE false
ENV HOST_AUTO ""
ENV HOST_IPV4 ""
ENV HOST_IPV6 ""
ENV MAXMIND_ACCOUNT_ID ""
ENV MAXMIND_LICENSE_KEY ""
ENV MODE prod
ENV RATE_LIMIT ""
ENV RATE_LIMIT_INTERVAL 1
ENV SHOW_ABOUT false
ENV SHOW_FAQ false
ENV SHOW_SUPPORT false
COPY ./docker /
COPY --from=geoipupdate-dependency /go/bin/geoipupdate /usr/bin/geoipupdate
COPY --from=build-dependencies /build/app /var/www/app
COPY --from=build-dependencies /build/vendor /var/www/vendor
COPY --from=build-html /build/app/src/Renderer/Templates /var/www/app/src/Renderer/Templates
COPY --from=build-html /build/html /var/www/html
RUN mkdir -p /var/databases
ENTRYPOINT [ "/entrypoint.sh" ]
