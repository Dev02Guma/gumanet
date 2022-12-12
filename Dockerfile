FROM gumadesarrollo/php:7.4-nginx-sqlsrv-prod

ARG ARG_APP_NAME=gumanet

ENV APP_NAME=${ARG_APP_NAME} \
    PHP_FPM_LISTEN=/run/php-fpm.sock \
    NGINX_LISTEN=80 \
    NGINX_ROOT=/app/${ARG_APP_NAME}/public \
    NGINX_INDEX=index.php \
    NGINX_CLIENT_MAX_BODY_SIZE=25M \
    NGINX_PHP_FPM=unix:/run/php-fpm.sock \
    NGINX_FASTCGI_READ_TIMEOUT=60s \
    NGINX_FASTCGI_BUFFERS='8 8k' \
    NGINX_FASTCGI_BUFFER_SIZE='16k'

COPY default.tmpl /kool/default.tmpl

WORKDIR /app

RUN mkdir ${ARG_APP_NAME}

WORKDIR /app/${ARG_APP_NAME}

COPY . .
RUN composer install --ignore-platform-reqs

RUN chmod -R 777 storage

EXPOSE 80