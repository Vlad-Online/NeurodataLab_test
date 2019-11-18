FROM php:7.3-cli
RUN rm /etc/apt/preferences.d/no-debian-php

RUN apt-get update && \
    apt-get install -y  \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql bcmath pcntl sockets
RUN  pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY composer.json ./
RUN curl -sS https://getcomposer.org/installer | php -- \
            --filename=composer \
            --install-dir=/usr/local/bin && \
            echo "alias composer='composer'" >> /root/.bashrc
WORKDIR /var/service
ENV XDEBUG_CONFIG idekey=PHPSTORM
ENV PHP_IDE_CONFIG serverName=service
#ENTRYPOINT php -dxdebug.remote_host=172.20.0.1 artisan rabbitevents:listen user.add
ENTRYPOINT php artisan rabbitevents:listen user.add
