FROM php:8.2-apache

# Configuration de l'image de l'application Laravel
WORKDIR /var/www/html
COPY . .

# Ajout du fichier .htaccess avec la directive DirectoryIndex
COPY public/.htaccess /var/www/html/.htaccess

# Configuration du serveur Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Installation des paquets et dépendances
RUN apt-get update \
 && apt-get install -qq -y --no-install-recommends \
 cron \
 vim \
 locales coreutils apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev;

# Configuration des locales
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
 echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
 locale-gen

# Installation de Composer
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
 mv composer.phar /usr/local/bin/composer

# Installation et configuration des extensions PHP
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql mysqli gd opcache intl zip calendar dom mbstring zip gd xsl && a2enmod rewrite
RUN pecl install apcu && docker-php-ext-enable apcu

# Installation de l'extension amqp
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
 install-php-extensions amqp