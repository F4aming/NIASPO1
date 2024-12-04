FROM php:7.4-apache

# Установка необходимых расширений PHP
RUN docker-php-ext-install mysqli

# Копируем файлы в контейнер
COPY . /var/www/html/

# Настройка прав доступа
RUN chown -R www-data:www-data /var/www/html
