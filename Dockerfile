FROM php:7.4-fpm-alpine

RUN apk add imap-dev yarn openldap-dev krb5-dev zlib-dev wget git fcgi libpng-dev libmemcached-dev sudo libzip-dev icu-dev rabbitmq-c-dev \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql pdo imap zip ldap mysqli bcmath opcache gd sockets intl \
    && apk add autoconf \
        g++ \
        make \
    && pecl install apcu && docker-php-ext-enable apcu \
    && pecl install memcached && docker-php-ext-enable memcached \
    && pecl install amqp && docker-php-ext-enable amqp \
    && apk add ca-certificates \
#cleanup
    && apk del autoconf g++ wget make \
    && rm -rf /tmp/* /var/cache/apk/* \
# composer
    && cd /usr/bin/ && wget -O composer https://getcomposer.org/download/2.0.8/composer.phar && chmod +x /usr/bin/composer \
# fix log path
    && sed -i "s/error_log.*/error_log = \/var\/log\/php7\.4\-fpm\.error.log/g" /usr/local/etc/php-fpm.d/docker.conf \
    && sed -i "s/access.log.*/access.log = \/var\/log\/php7\.4\-fpm\.access.log/g" /usr/local/etc/php-fpm.d/docker.conf \
    && ln -sf /dev/null /var/log/php7.4-fpm.access.log \
    && ln -sf /proc/1/fd/2 /var/log/php7.4-fpm.error.log

# optimize php-fpm
RUN sed -i "s/pm.max_children.*/pm.max_children = 15/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/pm.start_servers.*/pm.start_servers = 5/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/pm.min_spare_servers.*/pm.min_spare_servers = 5/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/pm.max_spare_servers.*/pm.max_spare_servers = 10/g" /usr/local/etc/php-fpm.d/www.conf

# iconv fix
RUN apk add gnu-libiconv --update-cache --repository http://dl-cdn.alpinelinux.org/alpine/edge/community/ --allow-untrusted
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php

# install nginx
USER root
RUN apk add nginx \
    && mkdir -p /run/nginx
COPY services/configs/nginx.conf /etc/nginx/http.d/default.conf
RUN ln -sf /proc/1/fd/1 /var/log/nginx/access.log \
    && ln -sf /proc/1/fd/2 /var/log/nginx/error.log

# supervisor.d
RUN apk add supervisor
RUN mkdir /etc/supervisor.d
COPY services/configs/supervisor.d/ /etc/supervisor.d/

# change to www-data user
RUN rm -rf /var/www/* && chown www-data.www-data -R /var/www

USER www-data

RUN set -eux; \
	composer global require "symfony/flex" --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
	composer clear-cache
ENV PATH="${PATH}:/home/www-data/.composer/vendor/bin"

# prevent the reinstallation of vendors at every changes in the source code
COPY --chown=www-data:www-data composer.json composer.lock symfony.lock /var/www/
COPY --chown=www-data:www-data bin/console /var/www/bin/console
COPY --chown=www-data:www-data .env /var/www/.env
RUN chmod +x /var/www/bin/console

# install deps
RUN cd /var/www && composer install --no-autoloader --no-scripts \
    && composer clear-cache

# install app
COPY --chown=www-data:www-data ./ /var/www
RUN cd /var/www && composer dump-autoload -o --apcu \
    && composer run-script post-install-cmd
RUN cat /dev/null > /var/www/.env

# setup php.ini overrides
COPY services/configs/php/ /usr/local/etc/php/conf.d/
#override php.ini with the corresponding environment settings for preloading
ARG ENVIRONMENT_KEY=dev
COPY services/configs/php_${ENVIRONMENT_KEY}/ /usr/local/etc/php/conf.d/

USER root

WORKDIR /var/www

ENTRYPOINT ["supervisord", "-n", "-c", "/etc/supervisord.conf"]

EXPOSE 80