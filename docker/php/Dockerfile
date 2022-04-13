FROM php:8.0.15-fpm-alpine3.15

COPY --from=composer:2.2.6 /usr/bin/composer /usr/bin/composer

RUN apk update && \
	apk add --no-cache cmake freetype freetype-dev git gnutls-dev icu-dev icu-libs libjpeg-turbo libjpeg-turbo-dev \
		libpng libpng-dev libressl-dev libwebp libwebp-dev libxml2-dev libxpm libxpm-dev libzip libzip-dev \
		openssh-client zlib-dev && \
	docker-php-ext-configure gd --enable-gd --with-webp --with-jpeg --with-xpm --with-freetype && \
	docker-php-ext-configure zip --with-zip && \
	pecl install xdebug && \
	docker-php-ext-install -j$(nproc) gd intl pdo_mysql soap zip && \
	docker-php-ext-enable xdebug && \
	apk del --purge cmake freetype-dev gnutls-dev icu-dev libjpeg-turbo-dev libpng-dev libressl-dev libwebp-dev \
		libxml2-dev libxpm-dev libzip-dev zlib-dev

COPY php-custom.ini /usr/local/etc/php/conf.d/zz-custom.ini
COPY sendmail /usr/sbin/
RUN chmod 0755 /usr/sbin/sendmail

ARG USER_ID
ARG GROUP_ID

RUN if [ ${GROUP_ID:-0} -ne 0 ]; \
		then addgroup --gid ${GROUP_ID} docker-container-user; \
		else addgroup docker-container-user; fi && \
	if [ ${USER_ID:-0} -ne 0 ]; \
		then adduser --disabled-password --uid ${USER_ID} --ingroup docker-container-user docker-container-user; \
		else adduser --disabled-password --ingroup docker-container-user docker-container-user; fi && \
	mkdir -p /app/vendor /app/vendor/netvor /home/docker-container-user/.composer && \
	chown -R docker-container-user:docker-container-user /app /home/docker-container-user/.composer
USER docker-container-user

RUN mkdir -p -m 0700 ~/.ssh && ssh-keyscan bitbucket.org >> ~/.ssh/known_hosts
COPY --chown=docker-container-user:docker-container-user id_rsa id_rsa.pub /home/docker-container-user/.ssh/
RUN chmod 0600 /home/docker-container-user/.ssh/id_rsa*

WORKDIR /app
