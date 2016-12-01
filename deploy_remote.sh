#!/bin/bash

NEED_UPDATE=/home/vz/vz-blog/upd
cd /home/vz/vz-blog/

if [ -f $NEED_UPDATE ]; then
    echo 'Need update';
    rm /home/vz/vz-blog/upd
    tar -xzf /home/vz/vz-blog/grow-deploy.tar.gz
    rm -rf /var/www/hobby-work.ru/public
    mv build/ /var/www/hobby-work.ru/public
    chown www-data:vz -R /var/www/hobby-work.ru/public
fi
