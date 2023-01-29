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
const sass = require('gulp-sass')(require('sass'));
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
    this.saveDocument(true);
  });

  const indexRawJson = fs.readFileSync(dataFile, 'utf8')
    .replace('window.rawData=', '');

  const json = JSON.parse(indexRawJson);
      
  json.map(function (p) {
    idx.addDoc({
      id: p.id,
      title: p.title,
      content: p.content
    });
  });

  await fs.writeFile(indexFile, "window.index="+JSON.stringify(idx), () => {});
}

task('build-index', function() {
  return buildIndex(
    config.INDEX_DIR + 'data.json',
    config.INDEX_DIR + 'index.json' 
  );
});

task('grow-build', series('compile-sass', 'build-index'))

exports.build = parallel('grow-build')
exports.default = series('grow-build', parallel('watch-sass'))