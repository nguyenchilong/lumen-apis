FROM nginx:stable-alpine

ARG DOCKER_WORKING_DIR
ARG DOCKER_SOURCE_STORAGE_DIR

ADD ./nginx/nginx.conf /etc/nginx/
ADD ./nginx/default.conf /etc/nginx/conf.d/

RUN mkdir -p ${DOCKER_WORKING_DIR}
WORKDIR ${DOCKER_WORKING_DIR}
