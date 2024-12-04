FROM php:7.4-apache

# Устанавливаем необходимые зависимости и расширения PHP
RUN apt-get update && apt-get install -y \
    && docker-php-ext-install mysqli \
    && pecl install redis \
    && docker-php-ext-enable redis

# Копируем файлы приложения
COPY ./ /var/www/html/

# Указываем рабочую директорию
WORKDIR /var/www/html
