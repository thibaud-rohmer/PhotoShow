FROM alpine:3.10.1

MAINTAINER CYOSP <cyosp@cyosp.com>

RUN apk upgrade \
    && apk add \
      ca-certificates \
      git \
      zip \
      nginx \
      php7-session \
      php7-fpm \
      php7-gd \
      php7-xml \
      php7-simplexml \
      php7-exif \
      gd-dev \
      supervisor \
      fbida-exiftran \
	&& git clone https://github.com/thibaud-rohmer/PhotoShow.git /var/www/PhotoShow \
	&& apk del ca-certificates git

RUN deluser xfs
RUN adduser -u 33 -D -S -G www-data www-data

RUN echo "daemon off;" >> /etc/nginx/nginx.conf
RUN rm /etc/nginx/conf.d/default.conf
ADD nginx.conf /etc/nginx/conf.d/photoshow.conf
RUN mkdir -p /run/nginx /var/run/php

RUN sed -i -e 's/^.\+daemonize.\+$/daemonize = no/' /etc/php7/php-fpm.conf
RUN rm /etc/php7/php-fpm.d/www.conf
ADD fpm.conf /etc/php7/php-fpm.d/photoshow.conf

RUN mkdir -p /opt/PhotoShow/Photos /opt/PhotoShow/generated
RUN chown -R www-data:www-data /opt/PhotoShow/Photos /opt/PhotoShow/generated
RUN sed -i -e 's/$config->photos_dir.\+/$config->photos_dir = "\/opt\/PhotoShow\/Photos";/' /var/www/PhotoShow/config.php
RUN sed -i -e 's/$config->ps_generated.\+/$config->ps_generated = "\/opt\/PhotoShow\/generated";/' /var/www/PhotoShow/config.php

RUN sed -i -e 's/^\(\[supervisord\]\)$/\1\nnodaemon=true/' /etc/supervisord.conf
ADD supervisor.conf /etc/supervisor.d/photoshow.ini
RUN mkdir -p "/var/log/supervisor"

VOLUME ["/opt/PhotoShow", "/var/log"]
EXPOSE 80

CMD /usr/bin/supervisord
