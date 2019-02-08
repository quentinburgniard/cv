FROM php:7.3-apache
WORKDIR /var/www/html
RUN apt-get update && apt-get install -y wget libpng-dev git
RUN docker-php-ext-install gd
RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php --
COPY . /var/www/html
RUN php composer.phar install
RUN rm composer.phar
RUN chown -R www-data:www-data .