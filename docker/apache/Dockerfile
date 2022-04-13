FROM httpd:2.4.46-alpine

RUN sed -i \
	-e 's/^#\(LoadModule socache_shmcb_module\)/\1/' \
	-e 's/^#\(LoadModule proxy_module\)/\1/' \
	-e 's/^#\(LoadModule proxy_fcgi_module\)/\1/' \
	-e 's/^#\(LoadModule ssl_module\)/\1/' \
	-e 's/^#\(LoadModule http2_module\)/\1/' \
	-e 's/^#\(LoadModule rewrite_module\)/\1/' \
	-e '$aInclude conf/httpd-custom.conf' \
	/usr/local/apache2/conf/httpd.conf

COPY *.conf server.crt server.key /usr/local/apache2/conf/

ARG USER_ID
ARG GROUP_ID

RUN if [ ${GROUP_ID:-0} -ne 0 ]; \
		then addgroup --gid ${GROUP_ID} docker-container-user; \
		else addgroup docker-container-user; fi && \
	if [ ${USER_ID:-0} -ne 0 ]; \
		then adduser --disabled-password --uid ${USER_ID} --ingroup docker-container-user docker-container-user; \
		else adduser --disabled-password --ingroup docker-container-user docker-container-user; fi && \
	mkdir -p /app/www && \
	chown -R docker-container-user:docker-container-user /app /usr/local/apache2/logs
USER docker-container-user

EXPOSE 443
EXPOSE 90
EXPOSE 453

WORKDIR /app/www
