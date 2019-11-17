FROM php:7.3-apache
EXPOSE 80
RUN rm /etc/apt/preferences.d/no-debian-php

# RUN apt-get update && \
#	apt-get -y install \
#	libmcrypt-dev \
#	libxml2-dev \
#	zlib1g-dev \
#	libzip-dev \
#	curl

RUN curl -sS https://getcomposer.org/installer | php -- \
            --filename=composer \
            --install-dir=/usr/local/bin && \
            echo "alias composer='composer'" >> /root/.bashrc && \
            composer

# RUN docker-php-ext-install zip bcmath pdo_mysql mysqli mbstring opcache soap

RUN a2enmod rewrite
COPY 000-default.conf /etc/apache2/sites-available/
WORKDIR /var/www
