FROM webdevops/php-nginx-dev:7.2

ENV MAGENTO_ROOT /app

COPY nginx/nginx.conf /opt/docker/etc/nginx/vhost.common.d/
RUN mkdir -p /usr/share/man/man1

ENV DEBIAN_FRONTEND="noninteractive"

RUN apt-get -y update
RUN apt-get install -y --no-install-recommends apt-utils dialog
RUN apt-get -y upgrade

WORKDIR /app
