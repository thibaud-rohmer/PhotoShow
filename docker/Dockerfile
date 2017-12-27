FROM debian:stretch-slim
MAINTAINER CYOSP <cyosp@cyosp.com>

RUN DEBIAN_FRONTEND=noninteractive apt update && apt-get upgrade -yq

RUN DEBIAN_FRONTEND=noninteractive apt install -yq \
	--no-install-recommends \
	git-core \
	nginx \
	php-fpm \
	php-gd \
	php-xml \
	libgd-dev \
	supervisor \
	exiftran

RUN mkdir -p /opt/PhotoShow/Photos /opt/PhotoShow/generated
RUN chown -R www-data:www-data /opt/PhotoShow/Photos /opt/PhotoShow/generated

RUN git clone https://github.com/thibaud-rohmer/PhotoShow.git /var/www/PhotoShow
RUN sed -i -e 's/$config->photos_dir.\+/$config->photos_dir = "\/opt\/PhotoShow\/Photos";/' /var/www/PhotoShow/config.php
RUN sed -i -e 's/$config->ps_generated.\+/$config->ps_generated = "\/opt\/PhotoShow\/generated";/' /var/www/PhotoShow/config.php

RUN rm -f /etc/nginx/sites-enabled/default
RUN echo "daemon off;" >> /etc/nginx/nginx.conf
RUN mkdir -p /var/run/php
ADD nginx.conf /etc/nginx/conf.d/photoshow.conf

RUN sed -i -e 's/^.\+daemonize.\+$/daemonize = no/' /etc/php/7.0/fpm/php-fpm.conf
ADD fpm.conf /etc/php/7.0/fpm/pool.d/photoshow.conf

RUN sed -i -e 's/^\(\[supervisord\]\)$/\1\nnodaemon=true/' /etc/supervisor/supervisord.conf
ADD supervisor.conf /etc/supervisor/conf.d/photoshow.conf

VOLUME ["/opt/PhotoShow", "/var/log"]
EXPOSE 80

CMD /usr/bin/supervisord