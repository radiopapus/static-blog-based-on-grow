FROM alpine:3.8

MAINTAINER Viktor Zharina <viktorz1986@gmail.com>

ARG grow_version

RUN echo -e "\e[31m Grow version $grow_version will be installed \e[0m"

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
    git \
  && python --version \
  && pip install --upgrade pip wheel \
  && pip install --upgrade grow==$grow_version \
  && rm -rf /var/cache/apk/* \
  && mkdir -p /root/.ssh/

RUN echo -e "\e[31m Grow: `grow --version` was installed\e[0m"

COPY . src/

#RUN mv src/id_rsa /root/.ssh/ && mv src/id_rsa.pub /root/.ssh/