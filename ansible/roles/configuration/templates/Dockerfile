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
    ffmpeg \
    libavcodec-extra-53 \
	supervisor
    
ADD inc /var/www/photoshow/inc
ADD src /var/www/photoshow/src
ADD user /var/www/photoshow/user
ADD index.php /var/www/photoshow/
ADD config.php /var/www/photoshow/

# VOLUME ["/opt/photoshow/photos", "{{ generated_dir }}/Thumbs", "{{ generated_dir }}/Conf", "{{ log_dir }}"]

RUN sed -i -e 's/^\(\[supervisord\]\)$/\1\nnodaemon=true/' /etc/supervisor/supervisord.conf
ADD supervisor-photoshow.conf /etc/supervisor/conf.d/photoshow.conf

RUN mkdir -p {{ generated_dir }}/Thumbs
RUN mkdir -p {{ generated_dir }}/Conf
RUN mkdir -p {{ log_dir }}/


ADD accounts.xml {{ generated_dir }}/Conf/
ADD groups.xml {{ generated_dir }}/Conf/

# RUN mkdir -p {{ photo_dir }}
RUN chown -R www-data:www-data {{ generated_dir }}

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
