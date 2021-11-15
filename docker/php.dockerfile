FROM php:7.4-fpm-alpine

ARG DOCKER_WORKING_DIR
ARG DOCKER_SOURCE_STORAGE_DIR
ARG DOCKER_NGINX_USER

RUN addgroup -g 1000 ${DOCKER_NGINX_USER}
RUN adduser -G ${DOCKER_NGINX_USER} -g ${DOCKER_NGINX_USER} -s /bin/sh -D ${DOCKER_NGINX_USER}

ADD ./php/www.conf /usr/local/etc/php-fpm.d/

RUN mkdir -p ${DOCKER_WORKING_DIR}
WORKDIR ${DOCKER_WORKING_DIR}

RUN apk add gcc g++ autoconf make
RUN apk add --no-cache zip libzip-dev
RUN docker-php-ext-install zip
RUN docker-php-ext-install mysqli pdo pdo_mysql
