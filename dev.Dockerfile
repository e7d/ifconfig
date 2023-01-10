FROM debian:buster-slim AS dependencies
RUN export DEBIAN_FRONTEND=noninteractive \
    && apt-get -qy update \
    && apt-get -qy upgrade
RUN apt-get install -qy lsb-release apt-transport-https ca-certificates wget \
    && wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
    && echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" |tee /etc/apt/sources.list.d/php.list \
    && apt-get -qy update \
    && apt-get -qy upgrade \
    && apt-get install -qy apache2 bind9 cron php8.2-dev php8.2-fpm php8.2-mbstring php8.2-opcache php8.2-redis php8.2-xml redis-server
RUN rm /var/www/html/index.html
RUN a2enmod expires headers proxy_fcgi rewrite setenvif \
    && a2enconf php8.2-fpm
RUN apt-get -qy autoremove --purge php8.2-dev \
    && apt-get clean \
    && rm -r /var/cache/apt/archives/* /var/lib/apt/lists/*

FROM golang AS geoipupdate-dependency
RUN env GO111MODULE=on go install github.com/maxmind/geoipupdate/v4/cmd/geoipupdate@latest

FROM dependencies
ENV DATABASE_AUTO_UPDATE ""
ENV DATABASE_DIR /var/databases
ENV DNS_CACHE false
ENV HOST_AUTO ""
ENV HOST_IPV4 ""
ENV HOST_IPV6 ""
ENV MAXMIND_ACCOUNT_ID ""
ENV MAXMIND_LICENSE_KEY ""
ENV MODE dev
ENV RATE_LIMIT ""
ENV RATE_LIMIT_INTERVAL 1
ENV SHOW_ABOUT false
ENV SHOW_FAQ false
ENV SHOW_SUPPORT false
COPY ./docker /
RUN a2enconf environment
COPY --from=geoipupdate-dependency /go/bin/geoipupdate /usr/bin/geoipupdate
WORKDIR /var/www
ENTRYPOINT [ "/entrypoint.sh" ]
STOPSIGNAL SIGWINCH
EXPOSE 80
