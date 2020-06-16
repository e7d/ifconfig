FROM php:7-apache
ENV RATE_LIMIT_INTERVAL 1
COPY ./docker /
RUN a2enmod expires headers rewrite
