<?php

use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Defines application features from the specific context.
 */
class VideoContext extends RawMinkContext {

  /**
   * Initializes context.
   *
   * Every scenario gets it's own context object.
   */
  public function __construct() {
    // Initialize your context here.
  }

  /**
   * Checks that Wistia embed code is present.
   *
   * @Given I see wistia embed code
   */
  public function assertPresenceWistiaEmbedCode() {
    $element = $this->getSession()->getPage();
    $nodes = $element->findAll('css', 'div.video-embed');
    foreach ($nodes as $node) {
      if ($node->isVisible()) {
        continue;
      }
      else {
        throw new \Exception("Wistia video UI not visible.");
      }
    }
  }

  /**
   * Checks that a Wistia video UI is visible.
   *
   * @Then I should see the wistia video ui
   */
  public function assertVisibilityWistiaVideoUi() {
    $element = $this->getSession()->getPage();
    $embeds = $element->findAll('css', 'div.wistia_embed');
    foreach ($embeds as $embed) {
      $embed->findAll('css', 'play-button');
    }
    $nodes = $element->findAll('css', 'div.w-ui-container');
    foreach ($nodes as $node) {
      if ($node->isVisible()) {
        continue;
      }
      else {
        throw new \Exception("Wistia video UI not visible.");
      }
    }
  }

  /**
   * Checks that a Wistia video is visible.
   *
   * @Then I should see the wistia video
   */
  public function assertVisibilityWistiaVideo() {
    $element = $this->getSession()->getPage();
    $nodes = $element->findAll('css', 'div.w-video-wrapper > video');
    foreach ($nodes as $node) {
      if ($node->isVisible()) {
        continue;
      }
      else {
        throw new \Exception("Wistia video not visible.");
      }
    }
  }

  /**
   * Checks that Wistia video UI in container are loaded and playable.
   *
   * @Then The videos inside :wistia_embed_container should be loaded and playable via :play_button
   */
  public function assertWistiaVideosLoadedAndPlayable($wistia_embed_container, $play_button) {
    $element = $this->getSession()->getPage();
    $containers = $element->findAll('css', $wistia_embed_container);
    if (empty($containers)) {
      throw new \Exception("Wistia video container $wistia_embed_container not found");
    }

    foreach ($containers as $container) {
      $videos = $container->findAll('css', 'div.image-placeholder');
      if (empty($videos)) {
        throw new \Exception("Wistia videos not found in container $wistia_embed_container");
      }

      foreach ($videos as $video) {
        $id = $video->getAttribute('data-video');

        // First verify the presence of the dialogs and contents.
        $data_video_modal_id = 'wistia-' . $id . '-1_popover_container';
        $video_dialog = $element->findById($data_video_modal_id);
        if (empty($video_dialog)) {
          throw new \Exception("Wistia videos dialog not found for video id $id");
        }

        $video_ui = $video_dialog->find('css', 'div.w-ui-container');
        if (empty($video_ui)) {
          throw new \Exception("Wistia video ui not found in dialog for video id $id");
        }

        $video_embed = $video_dialog->find('css', 'div.w-video-wrapper > video');
        if (empty($video_embed)) {
          throw new \Exception("Wistia video embed not found in dialog for video id $id");
        }

        // Click play!
        $video->click();
        sleep(3);
        $video_overlay_id = '#wistia-' . $id . '-1_popover_overlay';
        $this->assertSession()->elementExists('css', $video_overlay_id);

        // Close dialog.
        $video_dialog->find('css', 'button.wistia_placebo_close_button')->click();
        sleep(2);
        // Check if video is now not visble.
        $this->assertSession()->elementNotExists('css', '.wistia_popover_overlay');

      }
    }
  }

  /**
   * Press button with class.
   *
   * Example: Then I should see a youtube video iframe named "Commercial Embed".
   *
   * @then I should see a youtube video iframe named :name
   */
  public function assertVisibilityYouTubeVideoIframe($name) {
    $iframe = $this->getSession()->getPage()->find('css', 'iframe');
    if (!empty($iframe)) {
      $iframe_name = $iframe->getAttribute('name');
      if ($iframe_name == $name) {
        $this->getSession()->switchToIframe($name);
        $iframe_body = $this->getSession()->getPage()->find('css', 'body');
        if (empty($iframe_body)) {
          throw new Exception("Iframe named $name found, but it's body is empty");
        }
      }
      else {
        throw new Exception("YouTube $name iFrame not found");
      }
    }
    else {
      throw new Exception("Not a single iFrame element not found");
    }
  }

}
