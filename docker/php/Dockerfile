# official PHP docker repository.
# @see: https://hub.docker.com/_/php
FROM php:8.1-fpm-alpine

# --------- Setup build options
ARG WORK_DIR='/var/www'

RUN apk update

# --------- Install dependancies
RUN apk add --update --no-cache \
        bash \
        curl \
        shadow \
        icu-libs \
        libintl \
        libzip \
        aria2 \
        gettext \
        patch

# --------- Install build dependancies
RUN apk add --update --no-cache --virtual .docker-php-global-dependancies \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        gettext-dev \
        gmp-dev \
        icu-dev \
        oniguruma-dev \
        libxml2-dev \
        ldb-dev \
        libzip-dev \
        autoconf \
        g++ \
        make \
        pcre-dev \
        wget

# --------- Install php extensions
RUN php -m && \
    docker-php-ext-configure bcmath --enable-bcmath && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-configure gettext && \
    docker-php-ext-configure gmp && \
    docker-php-ext-configure intl --enable-intl && \
    docker-php-ext-configure mbstring --enable-mbstring && \
    docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-configure pcntl --enable-pcntl && \
    docker-php-ext-configure soap && \
    docker-php-ext-configure zip && \
    docker-php-ext-install bcmath \
        gd \
        gettext \
        gmp \
        intl \
        mbstring \
        opcache \
        pcntl \
        soap \
        dom \
        xml \
        zip && \
    php -m

# ENABLE CURL
RUN apk add --update curl-dev && \
    docker-php-ext-install curl && \
    apk del gcc g++ && \
    php -m;

# Enable MCRYPT
RUN apk add --update libmcrypt-dev && \
      pecl install mcrypt && \
      docker-php-ext-enable mcrypt && \
      php -m;

# Enable Memcached
RUN   apk add --update --no-cache \
          libevent \
          libmemcached-libs && \
      apk add --update --no-cache --virtual .docker-php-memcached-dependancies \
          cyrus-sasl-dev \
          libevent-dev \
          libmemcached-dev && \
      pecl install memcached && \
      docker-php-ext-enable memcached && \
      apk del .docker-php-memcached-dependancies && \
      php -m;

# Enable MySQL
RUN apk add --update --no-cache --virtual .docker-php-mysql-dependancies \
              mysql-client && \
          docker-php-ext-configure mysqli && \
          docker-php-ext-configure pdo_mysql && \
          docker-php-ext-install mysqli \
          pdo_mysql && \
          apk del .docker-php-mysql-dependancies && \
          php -m;

# Enable Redis
RUN pecl install redis && \
          docker-php-ext-enable redis && \
          php -m;

# Enable AWSClI
RUN apk add --no-cache \
            python3 \
            py3-pip \
        && pip3 install --upgrade pip \
        && pip3 install \
            awscli;

# Enable Supervisor Integration
RUN apk add --no-cache \
            supervisor;

# Enable Loggly Integration
RUN apk add --no-cache \
            rsyslog rsyslog-mmjsonparse;

# Enable composer
RUN php -r "readfile('https://getcomposer.org/installer');" |  \
    php -- --install-dir=/usr/bin/ --filename=composer \


# Big clean
RUN apk del .docker-php-global-dependancies && \
    rm -rf /var/cache/apk/* && \
    docker-php-source delete

# User and ownership
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data \
    && mkdir -p /home/www-data/.composer/cache \
    && mkdir -p /var/log/php \
    && mkdir -p /var/log/crond \
    && mkdir -p /var/log/supervisord \
    && touch /var/log/php/php_error.log \
    && touch /var/log/php/php-fpm-access.log \
    && touch /var/log/php/php-fpm-error.log \
    && touch /var/log/php/php-cli-error.log \
    && find /var/log/php -type f -exec chmod 755 {} \; \
    && chown -R www-data:www-data /home/www-data /var/www \
    && chown -R www-data:www-data /var/log/php /var/log/crond /var/log/supervisord

# Change Working directory
WORKDIR $WORK_DIR

# Expose port
EXPOSE 9000

# Command
CMD ["php-fpm"]
