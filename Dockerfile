FROM composer:1.9.1 as composer
COPY ./composer.json ./composer.lock /app/
RUN composer install

FROM php:7.2-apache
RUN docker-php-ext-install bcmath
WORKDIR /var/www
RUN sed -ri -e 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/*.conf\
 && rmdir /var/www/html
RUN a2enmod headers rewrite
RUN echo "\
ServerName apollo\n\
Header always set Access-Control-Allow-Origin \"*\"\n\
Header always set Access-Control-Allow-Headers \"content-type\"\n\
RewriteEngine On\n\
RewriteCond %{REQUEST_METHOD} OPTIONS\n\
RewriteRule ^(.*)$ $1 [R=200,L]\n\
" > /etc/apache2/conf-available/apache2-custom.conf\
 && a2enconf apache2-custom
RUN echo "\
PassEnv SENTRY_DSN\n\
PassEnv SENTRY_DSN\n\
" > /etc/apache2/conf-enabled/expose-env.conf
COPY --from=composer /app/vendor /var/www/vendor
COPY ./public /var/www/public
COPY ./src /var/www/src
