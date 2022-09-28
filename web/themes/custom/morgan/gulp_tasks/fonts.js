const gulp = require("gulp");
const config = require('../buildconfig.js');
const gutil = require('gulp-util');
const rimraf = require('rimraf');

/* Build the fonts dist dir. */
gulp.task("fonts_build", ["fonts_clean"], (cb) => {
  gulp
    .src(config.fonts.source)
    .pipe(gulp.dest(config.fonts.dest));
  cb();
});

/* Watch fonts directory for changes. */
gulp.task("fonts_watch", (cb) => {
  gulp.watch(config.fonts.source, ["fonts"]);
  cb();
});

/* Remove the fonts dist dir. */
gulp.task('fonts_clean', function (done) {
  return rimraf(config.fonts.dest, function () {
    gutil.log('Fonts directory', gutil.colors.magenta(config.fonts.dest), 'deleted');
    return done();
  });
});
