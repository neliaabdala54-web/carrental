

FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli \
    && a2enmod rewrite headers

ENV PORT=10000

COPY . /var/www/html/
COPY certs/ca.pem /etc/ssl/certs/aiven-ca.pem

RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && sed -i 's/:80>/:10000>/' /etc/apache2/sites-available/000-default.conf \
    && chmod 644 /etc/ssl/certs/aiven-ca.pem

EXPOSE 10000

CMD ["apache2-foreground"]

