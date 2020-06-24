FROM php:7-apache AS dependencies
RUN DEBIAN_FRONTEND=noninteractive \
    && apt-get -qy update \
    && apt-get -qy install redis-server wget \
    && apt-get -qy autoremove --purge \
    && apt-get clean \
    && rm -r /var/cache/apt/archives/* /var/lib/apt/lists/*
RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis
RUN docker-php-ext-install opcache
RUN a2enmod expires headers rewrite

FROM dependencies
ENV MODE prod
ENV MAXMIND_LICENSE_KEY ""
ENV DATABASE_DIR /var/databases
ENV HOST_AUTO ""
ENV HOST_IPV4 ""
ENV HOST_IPV6 ""
ENV RATE_LIMIT 0
ENV RATE_LIMIT_INTERVAL 1
ENV SHOW_FAQ false
ENV SHOW_ABOUT false
COPY ./docker /
ENTRYPOINT [ "/entrypoint.sh" ]
CMD [ "apache2-foreground" ]
