FROM node:16.14.0-alpine3.15

RUN apk update && apk add --no-cache git openssh-client

ARG USER_ID
ARG GROUP_ID

RUN deluser --remove-home node && \
	if [ ${GROUP_ID:-0} -ne 0 ]; \
		then addgroup --gid ${GROUP_ID} docker-container-user; \
		else addgroup docker-container-user; fi && \
	if [ ${USER_ID:-0} -ne 0 ]; \
		then adduser --disabled-password --uid ${USER_ID} --ingroup docker-container-user docker-container-user; \
		else adduser --disabled-password --ingroup docker-container-user docker-container-user; fi && \
	mkdir -p /app/node_modules /home/docker-container-user/.cache/yarn && \
	chown -R docker-container-user:docker-container-user /app /home/docker-container-user/.cache/yarn
USER docker-container-user

RUN mkdir -p -m 0700 ~/.ssh && ssh-keyscan bitbucket.org >> ~/.ssh/known_hosts
COPY --chown=docker-container-user:docker-container-user id_rsa id_rsa.pub /home/docker-container-user/.ssh/
RUN chmod 0600 /home/docker-container-user/.ssh/id_rsa*

EXPOSE 3000

WORKDIR /app

CMD ["yarn", "start"]
