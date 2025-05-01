<?php

use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Defines application features from the specific context.
 */
class ImageContext extends RawMinkContext {

  /**
   * Initializes context.
   *
   * Every scenario gets it's own context object.
   */
  public function __construct() {
    // Initialize your context here.
  }

  /**
   * Checks that images with the lazyload class are lazyloaded.
   *
   * Example: Then images with lazyload class are lazyloaded.
   *
   * @Then images with lazyload class are lazyloaded
   */
  public function assertLazyloadImagesAreLazyloaded() {
    $element = $this->getSession()->getPage();
    $images = $element->findAll('css', 'img.lazyload');

    if (empty($images)) {
      throw new \Exception("No lazyload images found.");
    }

    foreach ($images as $image) {
      $data_src = $image->getAttribute('data-src');
      sleep(1);
      if ($image->isVisible()) {
        sleep(1);
        $src = $image->getAttribute('src');
        if ($data_src == $src) {
          continue;
        }
        else {
          throw new \Exception("Image was not loaded: $data_src");
        }
      }
      else {
        throw new \Exception("Image not visible: $data_src");
      }
    }
  }

}
