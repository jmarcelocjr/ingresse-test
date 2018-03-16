FROM php:7.2-fpm

RUN apt-get update -y \
	&& apt-get install -y libxml2-dev \
	&& docker-php-ext-install pdo pdo_mysql \
	&& pecl install -o -f redis \
	&& rm -rf /tmp/pear \
	&& docker-php-ext-enable redis \
	&& rm -rf /var/lib/apt/lists/*
