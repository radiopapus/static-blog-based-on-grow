FROM alpine:3.19

MAINTAINER Viktor Zharina <viktorz1986@gmail.com>

ARG grow_version

ENV LC_ALL=C.UTF-8
ENV LANG=C.UTF-8

COPY . src/

WORKDIR src/

RUN echo 'Cython < 3.0' > /tmp/constraint.txt

RUN adduser --disabled-password --gecos "" grow && \
    apk update && \
  apk add --no-cache --update yarn \
    zip libc6-compat \
    python3 py3-lxml py3-libxml2 py3-setuptools py3-pip py3-yaml \
    build-base libffi-dev libressl-dev libxml2-dev libxslt-dev \
    openssh-client g++ yaml-dev git nodejs \
    && python3 --version \

    && PIP_CONSTRAINT=/tmp/constraint.txt pip3 install --break-system-packages --no-cache-dir --upgrade pip wheel PyYAML==5.4 markupsafe \ 
  && pip3 install --break-system-packages --no-cache-dir --upgrade grow==$grow_version \
  && rm -rf /var/cache/apk/* \
  && rm -rf /tmp/*

RUN echo -e "\e[31m Grow installed\e[0m"

ENV PATH /src/node_modules/.bin:$PATH

RUN chmod +x startup.sh

CMD ["startup.sh"]