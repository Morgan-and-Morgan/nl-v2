<?php

use Behat\Mink\Exception\ElementNotFoundException;
use Behat\MinkExtension\Context\RawMinkContext;

use function PHPUnit\Framework\stringContains;

/**
 * Defines JS Logs features from the specific context.
 */
class AmpContext extends RawMinkContext {

  /**
   * Initializes context.
   *
   * Every scenario gets it's own context object.
   */
  public function __construct() {
    // Initialize your context here.
  }

  /**
   * Check for entry in JS logs.
   *
   * @Example: Then AMP should be valid
   *
   * @Then AMP should be valid
   */
  public function ampIsValid() {
    $url = $this->getSession()->getCurrentUrl();
    $validate = exec('cd web/themes/custom/morgan_amp; amphtml-validator ' . $url);
    if (!stringContains('PASS', $validate)) {
      throw new Exception('AMP is not valid, see browser console logs for more info: ' . $url . '#development=1');
    }
  }

}
