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

