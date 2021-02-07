FROM alpine:3.8

MAINTAINER Viktor Zharina <viktorz1986@gmail.com>

ARG grow_version

COPY . src/

WORKDIR src/

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
    nodejs \
    npm \
  && python --version \
  && pip install --upgrade pip wheel \
  && pip install --upgrade grow==$grow_version \
  && rm -rf /var/cache/apk/* \
  && mkdir -p /root/.ssh/ \
  && rm -rf /tmp/*

RUN echo -e "\e[31m Grow: `grow --version` was installed\e[0m"

ENV PATH /src/node_modules/.bin:$PATH

RUN chmod +x npm_startup.sh

CMD ["npm_startup.sh"]

#RUN mv src/id_rsa /root/.ssh/ && mv src/id_rsa.pub /root/.ssh/