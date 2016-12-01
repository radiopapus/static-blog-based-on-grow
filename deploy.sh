#!/bin/bash

DIST_PATH=/home/devel/grow/my-codelab/
GROW_PATH=/home/devel/grow/scripts/grow

$GROW_PATH build $DIST_PATH

TAR_NAME=grow-deploy.tar.gz
REMOTE_PATH=vz@hobby-work.ru:/home/vz/vz-blog

cd $DIST_PATH

touch upd
tar -czf $TAR_NAME build/
scp $DIST_PATH/$TAR_NAME $REMOTE_PATH
scp $DIST_PATH/upd $REMOTE_PATH
rm upd

