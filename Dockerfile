FROM  kooldev/php:7.4-nginx-sqlsrv-prod

ARG ARG_APP_NAME=gnet

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

WORKDIR /app/${ARG_APP_NAME}

COPY default.tmpl /kool/default.tmpl

COPY . .

RUN chmod -R 777 storage 

EXPOSE 80