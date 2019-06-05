#!/bin/bash
for OUTPUT in $(ls)
do
    convert -resize 1200 -monitor $OUTPUT ${OUTPUT:0:-4}.thumb.jpg
done
