FROM php:7.4-apache
WORKDIR /var/www/html
RUN apt-get update && apt-get install -y wget libpng-dev git locales
RUN localedef -i fr_FR -c -f UTF-8 -A /usr/share/locale/locale.alias fr_FR.UTF-8
ENV LANG fr_FR.utf8
RUN docker-php-ext-install gd
RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php --
COPY . /var/www/html
RUN php composer.phar install
RUN rm composer.phar && rm -rf /var/lib/apt/lists/* && apt remove -y git wget && apt autoremove -y
RUN chown -R www-data:www-data .