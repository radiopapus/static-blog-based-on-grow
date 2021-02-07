const {
  dest,
  series,
  parallel,
  src,
  task,
  watch
} = require('gulp');
const gulpAutoprefixer = require('gulp-autoprefixer');
const path = require('path');
const rename = require('gulp-rename');
const sass = require('gulp-sass');
const elasticlunr = require('elasticlunr');
require('./node_modules/lunr-languages/lunr.stemmer.support.js')(elasticlunr);
require('./node_modules/lunr-languages/lunr.ru.js')(elasticlunr);
require('./node_modules/lunr-languages/lunr.multi.js')(elasticlunr);

const fs = require('fs');


const config = {
  SASS_SOURCE_DIR: './source/sass/*.{sass,scss}',
  SASS_SOURCES: [
    './partials/**/*.{sass,scss}',
    './source/sass/**/*.{sass,scss}',
  ],
  SASS_OUT_DIR: './dist/',
  INDEX_DIR: './source/index/'
};

task('compile-sass', function(cb) {
  return src(config.SASS_SOURCE_DIR)
    .pipe(sass({
      outputStyle: 'compressed'
    })).on('error', sass.logError)
    .pipe(rename(function(path) {
      path.basename += '.min';
    }))
    .pipe(gulpAutoprefixer())
    .pipe(dest(config.SASS_OUT_DIR));
});

task('watch-sass', function() {
  watch(config.SASS_SOURCES, series('compile-sass'));
});

async function buildIndex(dataFile, indexFile) {
  const idx = elasticlunr(function () {
    this.use(lunr.multiLanguage('en', 'ru'));
    this.setRef('id');
    this.addField('title');
    this.addField('content');
    this.saveDocument(false);
  });

  const data = JSON.parse(fs.readFileSync(dataFile, 'utf8'));
      
  data.map(function (p) {
    idx.addDoc({
      id: p.id,
      title: p.title,
      content: p.content
    });
  });

  fs.writeFileSync(indexFile, "index = " + JSON.stringify(idx));
  await fs.writeFile(dataFile, "rawData = " + JSON.stringify(data), () => {});
}

task('build-index', function() {
  return buildIndex(
    config.INDEX_DIR + 'data.json',
    config.INDEX_DIR + 'index.json' 
  );
});

task('grow-build', parallel('compile-sass', 'build-index'))

exports.build = parallel('compile-sass')
exports.default = series('compile-sass', parallel('watch-sass'))