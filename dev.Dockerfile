FROM php:7-apache AS dependencies
RUN DEBIAN_FRONTEND=noninteractive \
    && apt-get -qy update \
    && apt-get -qy install bind9 redis-server wget \
    && apt-get -qy autoremove --purge \
    && apt-get clean \
    && rm -r /var/cache/apt/archives/* /var/lib/apt/lists/*
RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis
RUN docker-php-ext-install opcache
RUN a2enmod expires headers rewrite

FROM dependencies
ENV DATABASE_DIR /var/databases
ENV DNS_CACHE false
ENV HOST_AUTO ""
ENV HOST_IPV4 ""
ENV HOST_IPV6 ""
ENV MAXMIND_LICENSE_KEY ""
ENV MODE dev
ENV RATE_LIMIT ""
ENV RATE_LIMIT_INTERVAL 1
ENV SHOW_ABOUT false
ENV SHOW_FAQ false
COPY ./docker /
ENTRYPOINT [ "/entrypoint.sh" ]
CMD [ "apache2-foreground" ]
