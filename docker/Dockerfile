FROM php:8.3.10-fpm

RUN apt-get update -y && apt-get install -y openssl zip unzip git

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update && apt-get install -y libpq-dev 

RUN docker-php-ext-install pdo pdo_mysql

RUN php -m | grep mbstring 

WORKDIR /app

COPY . /app

RUN composer install

EXPOSE 9000

CMD ["php-fpm"]
