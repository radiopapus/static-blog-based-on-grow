FROM alpine:3.16

MAINTAINER Viktor Zharina <viktorz1986@gmail.com>

ARG grow_version

COPY . src/

WORKDIR src/

# Setup for ssh onto github
#RUN mkdir -p /root/.ssh
#ADD id_ed25519 /root/.ssh/id_ed25519
#RUN chmod 700 /root/.ssh/id_ed25519 && echo "Host github.com\n\tStrictHostKeyChecking no\n" >> /root/.ssh/config

RUN adduser --disabled-password --gecos "" grow && \
    apk update && \
  apk add --no-cache --update \
    python3 \
    python3-dev \
    py3-pip \
    build-base \
    libffi-dev \
    libressl-dev \
    openssh-client \
    g++ \
    yaml-dev \
    py3-yaml \
    git \
    nodejs \
    npm \
  && python3 --version \
  && pip3 install markupsafe==2.0.1 \
  && pip3 install --upgrade pip wheel \
  && pip3 install --global-option="--with-libyaml" --force pyyaml \
  && pip3 install --upgrade grow==$grow_version \
  && rm -rf /var/cache/apk/* \
  && rm -rf /tmp/*

RUN echo -e "\e[31m Grow installed\e[0m"

ENV PATH /src/node_modules/.bin:$PATH

RUN chmod +x startup.sh

CMD ["startup.sh"]