FROM alpine:3.8

MAINTAINER Viktor Zharina <viktorz1986@gmail.com>

ARG grow_version

COPY . src/

RUN cd src/ && apk update && \
  apk add --update \
    python \
    python-dev \
    py-pip \
    build-base \
    libffi-dev \
    libressl-dev \
    g++ \
    yaml-dev \
    git \
    nodejs \
    npm \
  && python --version \
  && pip install --upgrade pip wheel \
  && pip install --upgrade grow==$grow_version \
  && npm i gulp \
  && npm i gulp-autoprefixer \
  && npm i gulp-rename \
  && npm i gulp-sass \
  && rm -rf /var/cache/apk/* \
  && mkdir -p /root/.ssh/ \
  && rm -rf /tmp/*

RUN echo -e "\e[31m Grow: `grow --version` was installed\e[0m"

#RUN mv src/id_rsa /root/.ssh/ && mv src/id_rsa.pub /root/.ssh/