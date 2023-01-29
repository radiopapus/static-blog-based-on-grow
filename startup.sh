#!/bin/sh

npm i gulp-cli
npm i gulp
npm i gulp-autoprefixer
npm i gulp-rename
npm i gulp-sass && npm config set python "/usr/bin/python3"

npm i node-gyp && npm config set python "/usr/bin/python3"
npm i sass && npm config set python "/usr/bin/python3"

npm i elasticlunr
npm i git+https://github.com/weixsong/lunr-languages.git
