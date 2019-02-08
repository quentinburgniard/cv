FROM php:7.3-apache
WORKDIR /var/www/html
RUN apt-get update && apt-get install -y wget libpng-dev git locales
RUN localedef -i en_US -c -f UTF-8 -A /usr/share/locale/locale.alias en_US.UTF-8
ENV LANG en_US.utf8
RUN docker-php-ext-install gd
RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php --
COPY . /var/www/html
RUN php composer.phar install
RUN rm composer.phar && rm -rf /var/lib/apt/lists/* && apt-get remove git wget
RUN chown -R www-data:www-data .