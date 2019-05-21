FROM alpine:3.8Dockerfile
MAINTAINER Viktor Zharina <viktorz1986@gmail.com>

ARG grow_version

RUN apk update && \
  apk add --update \
    python \
    python-dev \
    py-pip \
    build-base \
    libffi-dev \
    libressl-dev \
    g++ \
    yaml-dev \
  && python --version \
  && pip install --upgrade pip wheel \
  && pip install --upgrade grow==$grow_version \
  && rm -rf /var/cache/apk/*

RUN echo -e "\e[31m Grow: `grow --version` \e[0m"

COPY . src/

EXPOSE 8080

ENTRYPOINT ["grow"]