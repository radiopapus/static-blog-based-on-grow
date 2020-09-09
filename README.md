# My homepage based on grow

## Docker
docker-compose up -d

## Usefull commands
convert video to ogv
ffmpeg -i $1 -acodec libvorbis -vcodec libtheora -f ogv $2.ogv

#!/bin/bash
for OUTPUT in $(ls)
do
    convert -resize 600 -monitor $OUTPUT ${OUTPUT:0:-4}.thumb.jpg
done

git hook pre-push

SSH_PATH='vz@viktor.zharina.info'

##install
docker-compose run --rm blog sh -c 'cd src && npm install' #instal npm modules

docker-compose run --rm blog sh -c 'cd src && grow build --clear-cache --deployment default'
cd build && sudo tar -czf b.tar.gz * --exclude=./*.gz && cd ..
exit 0
