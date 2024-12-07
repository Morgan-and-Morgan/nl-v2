// Do not run certain build jobs on the template repo.
if (process.env.CI_PROJECT_REPONAME == "mm-drupal-site-template-d9") {
  console.log( "Template repository does not deploy to Pantheon, nothing to do.");
}
else {

  // Stash dev URL, removing any trailing slash.
  const devURL = process.env.DEV_SITE_URL.replace(/\/$/, "");

  // Form the correct MDE site URL.
  const ciBranch = process.env.CI_BRANCH;
  const site = process.env.TERMINUS_SITE
  const multidevURL = 'https://' + ciBranch + '-' + site + '.pantheonsite.io';

  console.log('Comparing the following 2 sites...');
  console.log(devURL);
  console.log(multidevURL);

  // These are the paths to test, eventually this list will be long.
  const pathsToTest = {
      'UserLogin': '/user/login',
  }

  let scenariosToTest = [];

  for (let [key, value] of Object.entries(pathsToTest)) {
      scenariosToTest.push({
          label: key,
          url: multidevURL + value,
          referenceUrl: devURL + value,
          hideSelectors: [],
          removeSelectors: [],
          selectorExpansion: true,
          selectors: [
              'document',
          ],
          readyEvent: null,
          delay: 2000,
          misMatchThreshold: 15
      })
  }

  module.exports = {
      id: 'test',
      viewports: [{
              name: 'phone',
              width: 320,
              height: 480
          },
          {
              name: 'tablet',
              width: 1024,
              height: 768
          },
          {
              name: "desktop",
              width: 1920,
              height: 1080
          }
      ],
      scenarios: scenariosToTest,
      paths: {
          bitmaps_reference: 'backstop_data/bitmaps_reference',
          bitmaps_test: 'backstop_data/bitmaps_test',
          html_report: 'backstop_data/html_report',
          ci_report: 'backstop_data/ci_report'
      },
      report: ['browser', 'CI'],
      debug: false,
      engine: 'puppeteer',
      engineOptions: {
          args: ['--no-sandbox']
      },
      asyncCaptureLimit: 5,
      asyncCompareLimit: 50,
      debug: false,
      debugWindow: false
  };
}
