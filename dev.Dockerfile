FROM php:7-apache
COPY ./docker /
RUN a2enmod expires headers rewrite
