#!/bin/bash

NEED_UPDATE=path_to_upd_file
cd /home/vz/vz-blog/

if [ -f $NEED_UPDATE ]; then
    echo 'Need update';
    rm $NEED_UPDATE
    tar -xzf /path/to/grow-deploy.tar.gz
    rm -rf /var/www/viktor.zharina.info/public
    mv build/ /var/www/viktor.zharina.info/public
    chown user:group -R /var/www/viktor.zharina.info/public
fi
