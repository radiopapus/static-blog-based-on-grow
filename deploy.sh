#!/bin/bash

DIST_PATH=$(pwd)

GROW_PATH=grow

$GROW_PATH build $DIST_PATH --deployment default

TAR_NAME=grow-deploy.tar.gz
REMOTE_PATH=vz@viktor.zharina.info:/home/vz/vz-blog

cd $DIST_PATH

touch upd
tar -czf $TAR_NAME build/
scp $DIST_PATH/$TAR_NAME $REMOTE_PATH
scp $DIST_PATH/upd $REMOTE_PATH
rm upd
