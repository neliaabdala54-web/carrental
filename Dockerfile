
FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli \
    && a2enmod rewrite headers

ENV PORT=10000

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 10000

CMD ["sh", "-c", "sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf && sed -i 's/:80>/:10000>/' /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
