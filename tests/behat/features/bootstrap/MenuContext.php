<?php

use Behat\Mink\Exception\ElementNotFoundException;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Defines application features from the specific context.
 */
class MenuContext extends RawMinkContext {

  /**
   * Initializes context.
   *
   * Every scenario gets it's own context object.
   */
  public function __construct() {
    // Initialize your context here.
  }

  /**
   * Clicks link with specified id|title|alt|text.
   *
   * Example: When I follow "Locations" in the "main menu".
   * Example: And I follow "Locations" in the main menu.
   *
   * @When /^(?:|I )follow "(?P<link>(?:[^"]|\\")*)" in the main menu$/
   */
  public function clickMainMenuLink($text) {
    $main_menu = $this->getSession()->getPage()->findAll('css', 'ul.nav__list a, ul.nav__list button');

    foreach ($main_menu as $node) {
      if ($node->getText() === $text) {
        if ($node->isVisible()) {
          $node->click();
        }
        else {
          throw new \Exception("Main menu item with text \"$text\" not visible.");
        }
      }
    }
  }

  /**
   * Checks that an element with specified link is visible in main menu.
   *
   * @Then /^I should see "([^"]*)" visible in the main menu$/
   */
  public function assertElementVisibleInMainMenu($text) {
    $element = $this->getSession()->getPage();
    $nodes = $element->findAll('css', 'ul.nav__list a, ul.nav__list button');
    foreach ($nodes as $node) {
      if ($node->getText() === $text) {
        if ($node->isVisible()) {
          return;
        }
        else {
          throw new \Exception("Main menu item with text \"$text\" not visible.");
        }
      }
    }
    throw new ElementNotFoundException($this->getSession(), 'main-menu', 'link text', $text);
  }

  /**
   * Checks that an element with specified link is visible in main menu submenu links.
   *
   * @Then /^I should see "([^"]*)" visible in the main menu submenu links$/
   */
  public function assertElementVisibleInMainMenuSubmenu($text) {
    $element = $this->getSession()->getPage();
    $element_found = FALSE;
    $nodes = $element->findAll('css', 'a.nav__submenu-link');

    if (empty($nodes)) {
      throw new ElementNotFoundException($this->getSession(), 'main-menu', 'link text', 'no submenu links found');
    }

    foreach ($nodes as $node) {
      $element_text = trim($node->getText());
      if (empty($element_text)) {
        continue;
      }

      if ($element_text === $text) {
        if ($node->isVisible()) {
          $element_found = TRUE;
        }
      }
    }

    if (!$element_found) {
      throw new \Exception("Main menu sub menu link with text \"$text\" not visible.");
    }

  }

  /**
   * Checks that an element with specified link is visible in main menu submenu buttons.
   *
   * @Then /^I should see "([^"]*)" visible in the main menu submenu buttons$/
   */
  public function assertElementVisibleInMainMenuSubmenuButton($text) {
    $element = $this->getSession()->getPage();
    $element_found = FALSE;
    $nodes = $element->findAll('css', 'li.nav__submenu-item > button');
    foreach ($nodes as $node) {
      $element_text = trim($node->getText());
      if ($element_text === $text) {
        if ($node->isVisible()) {
          print_r("I found the button!");
          $element_found = TRUE;
        }
      }
    }

    if (!$element_found) {
      throw new \Exception("Main menu sub menu button with text \"$text\" not visible.");
    }

  }

}
