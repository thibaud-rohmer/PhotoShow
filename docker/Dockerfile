FROM debian:wheezy
MAINTAINER Anthony Prades <toony.github@chezouam.net>

RUN apt-get update
RUN DEBIAN_FRONTEND=noninteractive apt-get upgrade -yq

RUN DEBIAN_FRONTEND=noninteractive apt-get install -yq \
	--no-install-recommends \
	ca-certificates \
	git-core \
	vim \
	openssh-server \
	openssh-client \
	less \
	nginx \
	php5-fpm \
	php5-gd \
	libgd2-xpm \
	supervisor
    
RUN git clone https://github.com/thibaud-rohmer/PhotoShow.git /var/www/PhotoShow
RUN cd /var/www/PhotoShow && sed -i -e 's/$config->photos_dir.\+/$config->photos_dir = "\/opt\/PhotoShow\/Photos";/' config.php
RUN cd /var/www/PhotoShow && sed -i -e 's/$config->ps_generated.\+/$config->ps_generated = "\/opt\/PhotoShow\/thumbnails";/' config.php
    
RUN sed -i -e 's/^\(\[supervisord\]\)$/\1\nnodaemon=true/' /etc/supervisor/supervisord.conf
ADD supervisor-photoshow.conf /etc/supervisor/conf.d/photoshow.conf

RUN mkdir -p /var/www/PhotoShow
RUN mkdir -p /opt/PhotoShow/Photos
RUN mkdir -p /opt/PhotoShow/thumbnails
RUN chown -R www-data:www-data /opt/PhotoShow/Photos /opt/PhotoShow/thumbnails

RUN mkdir -p /root/.ssh
ADD photoshow.key.pub /root/.ssh/authorized_keys

ADD fpm-photoshow.conf /etc/php5/fpm/pool.d/photoshow.conf
RUN sed -i -e 's/^.\+daemonize.\+$/daemonize = no/' /etc/php5/fpm/php-fpm.conf

ADD photoshow.nginx /etc/nginx/sites-available/photoshow
RUN rm -f /etc/nginx/sites-enabled/default
RUN ln -s /etc/nginx/sites-available/photoshow /etc/nginx/sites-enabled/photoshow
RUN echo "daemon off;" >> /etc/nginx/nginx.conf

RUN mkdir -p /var/run/sshd

EXPOSE 80 22
CMD /usr/bin/supervisord
