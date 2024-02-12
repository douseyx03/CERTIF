# Use the php:8.2-apache base image
FROM php:8.2-apache

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy all files from the current directory into the container's working directory
COPY . .

# Add "ServerName localhost" to the end of the /etc/apache2/apache2.conf file
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Update package list and install necessary packages
RUN apt-get update \
    && apt-get install -qq -y --no-install-recommends \
    cron \
    vim \
    locales coreutils apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev;

# Set up locales and generate UTF-8 locales
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

# Set up locales and generate UTF-8 locales
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
    mv composer.phar /usr/local/bin/composer

# Configure and install PHP extensions
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql mysqli gd opcache intl zip calendar dom mbstring zip gd xsl && a2enmod rewrite
RUN pecl install apcu && docker-php-ext-enable apcu

# Add the install-php-extensions script to /usr/local/bin/ and make it executable
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions amqp