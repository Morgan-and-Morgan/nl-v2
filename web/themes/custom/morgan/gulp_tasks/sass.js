const gulp = require("gulp");
const sass = require("gulp-sass");
const config = require("../buildconfig.js");
const gutil = require("gulp-util");
const rimraf = require("rimraf");
var concat = require('gulp-concat');
var sourcemaps = require('gulp-sourcemaps');

/* Build/compile all sass files to css dist dir. */
// gulp.task("sass_build_global", ["sass_clean"], (cb) => {
//     gulp
//         .src(config.sass_global.source)
//         .pipe(sourcemaps.init())
//         .pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError))
//         .pipe(sourcemaps.mapSources(function(sourcePath, file) {
//           // source paths are prefixed with config.sass.sourcemapSrc
//           return config.sass.sourcemapSrc + sourcePath;
//         }))
//         .pipe(sourcemaps.write('./'))
//         .pipe(gulp.dest(config.sass_global.dest));
//     cb();
// });
gulp.task("sass_build_global", ["sass_clean"], (cb) => {
  compile(config.sass_global.source, config.sass_global.dest);
  cb();
});

/* Build/compile all sass files to css pages dir. */
gulp.task("sass_build_pages", ["sass_clean"], (cb) => {
  compile(config.sass_pages.source, config.sass_pages.dest);
  cb();
});

/* Build/compile block sass files to block dir. */
gulp.task("sass_build_blocks", ["sass_clean"], (cb) => {
  compile(config.sass_blocks.source, config.sass_blocks.dest);
  cb();
});

/**
 * Watch scss directories for changes.
 * ONLY compiles the file that was changed.
 */
gulp.task("sass_watch", () => {
  gulp.watch(config.sass_global.watchSource).on("change", function(file) {
    gutil.log("File:", gutil.colors.magenta(file.path), "was changed");
    gulp.start("sass_build_global");
  });
  gulp.watch(config.sass_pages.watchSource).on("change", function(file) {
    gutil.log("File:", gutil.colors.magenta(file.path), "was changed");
    if (compile(file.path, config.sass_pages.dest)) {
      gutil.log("File:", gutil.colors.green(config.sass_pages.dest + getFileName(file)), "and map updated");
    }
  });
  gulp.watch(config.sass_blocks.watchSource).on("change", function(file) {
    gutil.log("File:", gutil.colors.magenta(file.path), "was changed");
    if (compile(file.path, config.sass_blocks.dest)) {
      gutil.log("File:", gutil.colors.green(config.sass_blocks.dest + getFileName(file)), "and map updated");
    }
  });
});

/* Remove the css dist dir. */
gulp.task("sass_clean", function (done) {
  return rimraf(config.sass.dest, function () {
    gutil.log("SASS compiled CSS directory", gutil.colors.magenta(config.sass.dest), "deleted");
    return done();
  });
});

function compile(source, dest) {
  return gulp
    .src(source)
    .pipe(sourcemaps.init())
    .pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError))
    .pipe(sourcemaps.mapSources(function(sourcePath, file) {
      // source paths are prefixed with config.sass.sourcemapSrc
      return config.sass.sourcemapSrc + sourcePath;
    }))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest(dest));
}

function getFileName(file) {
  return "/" + file.path.substring(file.path.lastIndexOf("/") + 1, file.path.length);
}
