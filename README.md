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

docker-compose run blog sh -c 'cd src/ && grow build --clear-cache'
tar -czf b.tar.gz build/
scp -r b.tar.gz $SSH_PATH:/home/vz/scp_test/ && ssh $SSH_PATH 'cd scp_test;tar -zxf b.tar.gz;cp -R build/* /home/vz/blog/;rm -rf build b.tar.gz'
rm b.tar.gz

exit 0

