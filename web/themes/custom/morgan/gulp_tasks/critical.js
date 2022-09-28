const config = require('../buildconfig.js');
const critical = require('critical');
const fetch = require("node-fetch");
const gulp = require('gulp');
const gutil = require('gulp-util');
const path = require('path');
const fs = require('fs');
const osTmpdir = require('os-tmpdir');
const rimraf = require('rimraf');
const urljoin = require('url-join');

/* Allow requests to work with non-valid SSL certificates. */
process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";
process.setMaxListeners(20);

/* Build the critical css files. */
gulp.task('css_critical', ['css_critical_clean'], function (done) {
  Object.keys(config.critical.urls).map(function(url, index) {
    var baseUrl = 'https://test-nl-v2.pantheonsite.io/';
    var pageUrl = urljoin( baseUrl, url );
    var destCssPath = path.join(process.cwd(), config.critical.dest, config.critical.urls[url] + '.css' );

    const getData = async url => {
      try {
        const response = await fetch(url);
        const html = await response.text();

        var htmlString = html
          .replace(/href="\//g, 'href="' + urljoin(baseUrl, '/'))
          .replace(/src="\//g, 'src="' + urljoin(baseUrl, '/'));

        gutil.log('Generating critical css', gutil.colors.magenta(destCssPath), 'from', pageUrl);

        // Generate the critical CSS.
        try {
          await critical.generate({
            base: osTmpdir(),
            html: htmlString,
            target: destCssPath,
            dimensions: config.critical.dimensions,
            penthouse: {
              timeout: 1200000,
            },
            ignore: {
              atrule: ['@font-face'],
              decl: (node, value) => /url\(/.test(value),
            },
            rebase: asset => {`https://test-nl-v2.pantheonsite.io${asset.absolutePath}`}
          });

          // Show whether or not the file was created.
          if (fs.existsSync(destCssPath)) {
            gutil.log('Critical css generated', gutil.colors.green(destCssPath));
          }
          else {
            gutil.log('Critical css NOT generated', gutil.colors.red(destCssPath));
          }

        } catch (error) {
          console.log("[ERROR] Critical:", error);
        }

      } catch (error) {
        console.log("[ERROR] getData", error);
      }

    };

      // Call the async getData() function with our pageUrl.
      getData(pageUrl);

  });
});

/* Delete the css critical dist dir. */
gulp.task('css_critical_clean', function (done) {
    return rimraf(config.critical.dest, function() {
        gutil.log('Critical directory', gutil.colors.magenta(config.critical.dest), 'deleted');
        return done();
    });
});
