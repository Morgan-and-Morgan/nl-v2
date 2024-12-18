const gulp = require("gulp");
const sass = require('gulp-sass')(require('sass'));
const webpack = require("webpack-stream");

/** SASS TASKS **/
function buildSass() {
  return gulp
    .src("./scss/style.scss")
    .pipe(sass({ outputStyle: 'compressed' }).on("error", sass.logError))
    .pipe(gulp.dest("./dist"));
}

function watchSass() {
  gulp.watch(["./scss/style.scss", "./scss/**/*.scss"], buildSass);
}

/** JS TASKS **/
function buildJS() {
  return gulp
    .src("./js/main.js")
    .pipe(
      webpack({
        entry: "./js/main.js",
        output: {
          filename: "main-min.js"
        },
        mode: "production",
        optimization: {
          minimize: true,
        },
        plugins: [
          new (require("webpack")).ProvidePlugin({
            process: "process/browser", // Polyfill process for browser
          }),
        ],
        resolve: {
          fallback: {
            process: require.resolve("process/browser"), // Add process fallback
          },
        },
      })
    )
    .pipe(gulp.dest("./dist"));
}

function watchJS() {
  gulp.watch("./js/*.js", buildJS);
}

/** ASSETS TASKS **/
const watchAssets = gulp.parallel(watchSass, watchJS);
const buildAssets = gulp.series(buildSass, buildJS);

/** EXPORT TASKS **/
exports["sass:build"] = buildSass;
exports["sass:watch"] = watchSass;
exports["js:build-main-js"] = buildJS;
exports["js:watch"] = watchJS;
exports["assets:watch"] = watchAssets;
exports["assets:build"] = buildAssets;
