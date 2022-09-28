const gulp = require("gulp");
// eslint-disable-next-line no-unused-vars
const runSequence = require("run-sequence");
// eslint-disable-next-line no-unused-vars
const requireDir = require("require-dir")("./gulp_tasks");

// Assets watching. Build first, then watch.
gulp.task("sass:build:dev", ["sass:build", "sass:watch"]);
gulp.task("sass:build:prod", ["sass:build"]);

// Critical css.
gulp.task("sass:build:critical", ["css_critical"]);
gulp.task("sass:clean:critical", ["css_critical_clean"]);

// Sass compilation.
gulp.task("sass:build", ["sass_build_global", "sass_build_pages", "sass_build_blocks"]);
gulp.task("sass:clean", ["sass_clean"]);
gulp.task("sass:watch", ["sass_watch"]);

// Font related builds.
gulp.task("fonts:build:dev", ["fonts_build", "fonts_watch"]);
gulp.task("fonts:build:prod", ["fonts_build"]);
gulp.task("fonts:clean", ["fonts_clean"]);
gulp.task("fonts:watch", ["fonts_watch"]);
