FROM richarvey/nginx-php-fpm:1.7.0

RUN apk update && apk add libzip-dev zip mysql-client && rm -rf /var/cache/apk/*
RUN docker-php-ext-install opcache mysqli pdo_mysql zip

ADD docker/php-fpm/docker.conf /usr/local/etc/php-fpm.d/docker.conf
ADD docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf
ADD docker/php-fpm/php.ini /usr/local/etc/php/php.ini
ADD docker/php-fpm/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

ADD docker/nginx/nginx.conf /etc/nginx/nginx.conf
ADD docker/nginx/app.conf /etc/nginx/conf.d/default.conf

RUN echo "* * * * * php /var/www/html/artisan schedule:run" | crontab -
RUN mkdir -p /etc/supervisor/conf.d/
ADD docker/supervisor/crond.conf /etc/supervisor/conf.d/

ADD ./src /var/www/html
RUN chown -R nginx:nginx /var/www/html
RUN chmod -R 777 /var/www/html/storage/
