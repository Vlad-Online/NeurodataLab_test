FROM php:7.3-apache
EXPOSE 80
RUN rm /etc/apt/preferences.d/no-debian-php

RUN apt-get update && \
    apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql
RUN  pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN docker-php-ext-install bcmath
RUN docker-php-ext-install pcntl

RUN curl -sS https://getcomposer.org/installer | php -- \
            --filename=composer \
            --install-dir=/usr/local/bin && \
            echo "alias composer='composer'" >> /root/.bashrc && \
            composer

# RUN docker-php-ext-install zip bcmath pdo_mysql mysqli mbstring opcache soap

RUN a2enmod rewrite
COPY 000-default.conf /etc/apache2/sites-available/
WORKDIR /var/www/html
ENV XDEBUG_CONFIG idekey=PHPSTORM
ENV PHP_IDE_CONFIG serverName=service
ENTRYPOINT php -dxdebug.remote_host=172.18.0.1 artisan rabbitevents:listen user.add
#ENTRYPOINT ["XDEBUG_CONFIG="idekey=PHPSTORM" PHP_IDE_CONFIG="serverName=service" /usr/bin/php -dxdebug.remote_host=172.18.0.1  artisan rabbitevents:listen user.add"]
