FROM php:7-apache
ENV DATABASE_DIR /var/www/app/src/Reader/Databases/
ENV RATE_LIMIT_INTERVAL 1
COPY ./docker /
RUN a2enmod expires headers rewrite
ENTRYPOINT [ "/var/www/docker/entrypoint.sh" ]
CMD [ "apache2-foreground" ]
