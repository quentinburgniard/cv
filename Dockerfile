FROM php:7.3-apache
WORKDIR /var/www/html
ENV APACHE_RUN_DIR /var/www/html/web
RUN apt install -y wget
RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --quiet
COPY . /var/www/html
RUN composer install