FROM php:8.1-apache

# Copier le script
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Définir comme entrypoint
ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    libicu-dev \
    && docker-php-ext-install zip pdo pdo_mysql intl mysqli

RUN a2enmod rewrite

WORKDIR /var/www/html