FROM php:7.2-apache
COPY ./.docker/apache2-custom.conf /etc/apache2/conf-available/apache2-custom.conf
RUN a2enmod headers rewrite
RUN a2enconf apache2-custom
COPY ./public_html/ /var/www/html/
