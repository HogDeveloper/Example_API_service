ARG NGINX_VERSION

FROM nginx:${NGINX_VERSION}

ADD ./docker/nginx/conf.d/service_name.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/service_name