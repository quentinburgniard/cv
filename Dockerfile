FROM php:7.3-apache
WORKDIR /var/www/html
ENV APACHE_RUN_DIR /var/www/html/web
COPY . /var/www/html
RUN composer install