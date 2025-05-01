## Automated Testing
[![CircleCI](https://circleci.com/gh/Morgan-and-Morgan/ftp-v2.svg?style=svg&circle-token=d879adfa485a199598cc259678444a075e84d7c4)](https://app.circleci.com/pipelines/github/Morgan-and-Morgan/ftp-v2?branch=master)
### Behat
Behat is an open source Behavior-Driven Development framework for PHP. It is a tool to support you in delivering software that matters through continuous communication, deliberate discovery and test-automation.
Docs: https://docs.behat.org/en/latest/guides.html

### Mink/Gherkin
Mink is an open source browser controller/emulator for web applications, written in PHP 5.3.
Docs: http://mink.behat.org/en/latest/

### Getting Setup Locally
For running tests locally you'll need to setup a couple things in your terminal environment. The following should be added to your `~/.bash_profile` `~/.bashrc` or `~/.zshrc` file:

```
# Override Behat testing local URL
export BEHAT_PARAMS='{"extensions" : {"Behat\\MinkExtension" : {"base_url" : "http://SITE_TO_RUN_TESTS_AGAINST"}}}'

# behat stuff
alias behat="./vendor/bin/behat --config=tests/behat/behat-pantheon.yml"
alias chromedebug="/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --remote-debugging-address=0.0.0.0 --remote-debugging-port=9222 --disable-web-security --user-data-dir=/tmp/chrome_dev --disable-site-isolation-trials"
```
`export BEHAT_PARAMS` allows us to override the environment (base_url) for which the tests will be run against. This can be anything. Your local site, an MDE, dev, test or even live!

The `behat` command will need to be run from the root of your site directory. The alias is setup to use the `behat-pantheon.yml` file, located in `/tests/behat`. This ensures we're consistent when it comes to local vs Pantheon.

The `chromedebug` command should be run prior to executing behat tests. This will start a Chrome browser with remote debugging enabled which Behat needs to execute its tests against.

#### Update - Using Panther for JS tests
Instead of the `chromedebug` alias above, you'll want to run the following:
```
./vendor/bin/bdi detect drivers && ./drivers/chromedriver -v
```
This will fetch the chromedriver and run it in the background. Now you'll be able to run all of your Behat tests.

### Tagging
There are 2 primary tags to be aware of, `@prod` and `@dev`.
* The `@prod` tag should be added to ALL tests as they're indeded to be run whenever code is merged into master, these tests will be run against the dev site when this happens. KEY NOTE: `@prod` is for the Pantheon dev site.
* The `@dev` tag should be added to the majority of tests, but might make sense to be left off of some, such as search since search indexes are always broken when MDEs are created. They should not cause the MDE deployment to fail. However they should be run against the dev site (master branch). KEY NOTE: `@dev` is for Pantheon MDE sites.

Note: It is important to have at least one `@dev` and one `@prod` scenario in each test file. Reason being that with CCI parallelism in order to glob the tests it loops through the files. If a file is run with `--tags dev` and there are no `@dev` tags in the file, it will error and exit.

### Circle CI
Circle CI config for Behat can be found in `/.circleci/config.yml` as well as `/.ci/test/behat`. Parallelism is setup in `/.circleci/config.yml` under the `behat_test` job.
See https://circleci.com/docs/2.0/parallelism-faster-jobs/ for more info.