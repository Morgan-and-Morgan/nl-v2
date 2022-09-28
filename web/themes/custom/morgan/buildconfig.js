module.exports = {
  /**
   * To check which csss file is loading for each page
   * turn on twig debuging and search for CRITICAL CSS DEBUG on the source code.
   */
  critical: {
      dimensions: [{
          height: 400,
          width: 500,
      },
      {
          width: 1280,
          height: 900,
      }],
      dest: "dist/css/critical/",
      urls: {
          "/": "front",
      },
      penthouse: {
          timeout: 60000,
      },
  },
  sass: {
    sourcemapSrc: "../../../source/scss/",
    dest: "./dist/css",
  },
  sass_global: {
      source: "./source/scss/global-styles.scss",
      dest: "./dist/css/global",
      watchSource: [
        "./source/scss/global-styles.scss",
        "./source/scss/components/*.scss",
      ],
  },
  sass_pages: {
      source: "./source/scss/pages/pages.scss",
      dest: "./dist/css/pages",
      watchSource: "./source/scss/pages/*.scss",
  },
  sass_blocks: {
      source: "./source/scss/blocks/*.scss",
      dest: "./dist/css/blocks",
      watchSource: "./source/scss/blocks/*.scss",
  },
  webpack: {
      entryPoints: {
          "global": "./source/js/global.js",
          "home": "./source/js/home.js",
          "testimonials": "./source/js/testimonials.js",
      },
  },
  fonts: {
      source: "./source/fonts/**/*",
      dest: "./dist/fonts",
  },
};
