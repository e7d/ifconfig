FROM debian:stable-slim AS dependencies
RUN export DEBIAN_FRONTEND=noninteractive \
    && apt-get -qy update \
    && apt-get -qy upgrade
RUN apt-get install -qy lsb-release apt-transport-https ca-certificates wget \
    && wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
    && echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" |tee /etc/apt/sources.list.d/php.list \
    && apt-get -qy update \
    && apt-get -qy upgrade \
    && apt-get install -qy apache2 bind9 cron php8.3-fpm php8.3-gmp php8.3-mbstring php8.3-opcache php8.3-redis php8.3-xml redis-server \
    && apt-get install -qy php8.3-curl php8.3-dev unzip
RUN sed -i 's/;opcache.memory_consumption=128/opcache.memory_consumption=32/g' /etc/php/8.3/fpm/php.ini
RUN rm /var/www/html/index.html
RUN a2enmod expires headers proxy_fcgi rewrite setenvif \
    && a2enconf php8.3-fpm

FROM golang AS geoipupdate-dependency
RUN env GO111MODULE=on go install github.com/maxmind/geoipupdate/v4/cmd/geoipupdate@latest

FROM composer AS build-dependencies
WORKDIR /build
COPY . /build
RUN composer prod

FROM node AS build-html
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
RUN cd /tmp \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "copy('https://composer.github.io/installer.sig', 'composer-setup.sig');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === file_get_contents('composer-setup.sig')) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && php -r "unlink('composer-setup.sig');" \
    && mv composer.phar /usr/local/bin/composer

FROM common AS prod
ENV MODE prod
COPY --from=build-dependencies /build/app /var/www/app
COPY --from=build-dependencies /build/vendor /var/www/vendor
COPY --from=build-html /build/app/src/Renderer/Templates /var/www/app/src/Renderer/Templates
COPY --from=build-html /build/html /var/www/html
RUN mkdir -p /var/databases
RUN apt-get -qy autoremove --purge php8.3-curl php8.3-dev unzip \
    && apt-get clean \
    && rm -r /var/cache/apt/archives/* /var/lib/apt/lists/*
