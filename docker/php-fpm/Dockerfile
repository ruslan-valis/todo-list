FROM php:7.4-fpm-alpine

# Setup wait-for-it script
COPY wait-for-it.sh /usr/bin/wait-for-it
RUN chmod +x /usr/bin/wait-for-it

# Install system dependencies
RUN apk --update --no-cache add \
    git \
    curl \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Wait for database startup and start php-fpm
CMD composer install ; wait-for-it mysql:3306 -- bin/console doctrine:migrations:migrate ; bin/console doctrine:fixtures:load ;  php-fpm
EXPOSE 9000
