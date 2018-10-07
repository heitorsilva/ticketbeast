FROM php:7.2-alpine3.8

MAINTAINER Heitor Silva <heitor-silva@hotmail.com>

RUN mkdir /ticketbeast
WORKDIR /ticketbeast

ENV PAGER='busybox more'

# Locale
ENV LANG=en_US.UTF-8 \
    LANGUAGE=en_US.UTF-8 \
    LC_CTYPE=en_US.UTF-8 \
    LC_NUMERIC=en_US.UTF-8 \
    LC_TIME=en_US.UTF-8 \
    LC_COLLATE=en_US.UTF-8 \
    LC_MONETARY=en_US.UTF-8 \
    LC_MESSAGES=en_US.UTF-8 \
    LC_PAPER=en_US.UTF-8 \
    LC_NAME=en_US.UTF-8 \
    LC_ADDRESS=en_US.UTF-8 \
    LC_TELEPHONE=en_US.UTF-8 \
    LC_MEASUREMENT=en_US.UTF-8 \
    LC_IDENTIFICATION=en_US.UTF-8 \
    LC_ALL=en_US.UTF-8

# Timezone
RUN apk add --update --no-cache tzdata && \
    cp /usr/share/zoneinfo/Europe/Berlin /etc/localtime && \
    echo "Europe/Berlin" > /etc/timezone && \
    apk del tzdata

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# PHP Extensions
RUN apk add --update --no-cache argon2-dev autoconf bzip2-dev ca-certificates coreutils curl-dev dpkg dpkg-dev file freetype-dev g++ gcc icu-dev imagemagick-dev libc-dev libedit-dev libjpeg-turbo-dev libressl libressl-dev libpng-dev libsodium-dev libxml2-dev libxslt-dev make mariadb-dev openldap-dev pkgconf re2c && \
    docker-php-ext-install bcmath bz2 calendar curl gd intl json ldap mbstring mysqli opcache pdo pdo_mysql phar soap sockets sodium xml xmlrpc xmlwriter xsl zip
RUN pecl channel-update pecl.php.net && \
    pecl install imagick && \
    docker-php-ext-enable imagick

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0"]
