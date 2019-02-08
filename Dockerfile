FROM php:7.3-apache
RUN composer install
COPY web/ /usr/local/apache2/htdocs/
