FROM php:8.2.11-fpm

# Install composer
RUN echo "\e[1;33mInstall COMPOSER\e[0m"
RUN cd /tmp \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update

# Install useful tools
RUN apt-get -y install apt-utils nano wget dialog vim

# Install important libraries
RUN echo "\e[1;33mInstall important libraries\e[0m"
RUN apt-get -y install --fix-missing \
    apt-utils \
    build-essential \
    git \
    curl \
    libcurl4 \
    libcurl4-openssl-dev \
    zlib1g-dev \
    libzip-dev \
    zip \
    libbz2-dev \
    locales \
    libmcrypt-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev

# # Set the working directory
# WORKDIR /var/www

# # Copy the application code
# COPY . .

# # Set permissions for Laravel storage directory
# RUN mkdir -p /var/www/storage \
#     && chown -R www-data:www-data /var/www/storage \
#     && chmod -R 775 /var/www/storage

# # Set environment variable to allow composer to run as root
# ENV COMPOSER_ALLOW_SUPERUSER=1

# # Run composer install to install dependencies
# RUN composer install --no-interaction --optimize-autoloader

# # Set permissions for Laravel storage and cache directories
# RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
#     && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# RUN echo "\e[1;33mInstall important docker dependencies\e[0m"
# RUN docker-php-ext-install \
#     exif \
#     pcntl \
#     bcmath \
#     ctype \
#     curl \
#     iconv \
#     xml \
#     soap \
#     pcntl \
#     mbstring \
#     tokenizer \
#     bz2 \
#     zip \
#     intl

# # Install Postgre PDO
# RUN apt-get install -y libpq-dev \
#     && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
#     && docker-php-ext-install pdo pdo_pgsql pgsql


# docker exec -it --user=root my-money-laravel chmod -R 777 /var/www/storage