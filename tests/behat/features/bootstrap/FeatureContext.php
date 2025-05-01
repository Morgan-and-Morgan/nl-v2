<?php

use Behat\Mink\Exception\ElementNotFoundException;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext {

  /**
   * Initializes context.
   *
   * Every scenario gets it's own context object.
   */
  public function __construct() {
    // Initialize your context here.
  }

  /**
   * Wait for X amount of seconds.
   *
   * @Given /^I wait (\d+) seconds$/
   * @And /^I wait (\d+) seconds$/
   */
  public function iWaitSeconds($seconds) {
    sleep($seconds);
  }

  /**
   * Checks for the dataLayer object.
   *
   * @Then /^I should see the dataLayer$/
   */
  public function iSeeDataLayer() {
    $this->getSession()->wait(4000);
    $js = <<<JS
      function() {if (window.dataLayer.length > 0) { return "PASS"; } else { return "FAIL"; }}
    JS;
    $results = $this->getSession()->evaluateScript("$js");
    if ($results == "FAIL") {
      throw new Exception('dataLayer object not found');
    }
  }

  /**
   * Checks for the google_tag_manager object.
   *
   * @Then /^I should see the gtm object$/
   */
  public function iSeeGtm() {
    $this->getSession()->wait(1000);
    $js = <<<JS
      return function() {if (window.google_tag_manager != undefined) { return "PASS"; } else { return "FAIL"; }}
    JS;
    $results = $this->getSession()->evaluateScript("$js");
    if ($results == "FAIL") {
      throw new Exception('google_tag_manager object not found');
    }
  }

  /**
   * Selects an autosuggestion option.
   *
   * @Then /^I select autosuggestion option "([^"]*)"$/
   */
  public function selectAutosuggestionOption($text) {
    $session = $this->getSession();
    $element = $session->getPage()->find(
        'xpath',
        $session->getSelectorsHandler()->selectorToXpath('xpath', '//ul[@class="ui-autocomplete"]//a[@class="ui-menu-item-wrapper"]')
    );

    if (NULL === $element) {
      throw new \InvalidArgumentException(sprintf('Cannot find text: "%s"', $text));
    }

    $element->click();
  }

  /**
   * Checks that an element with specified link is on page.
   *
   * @Then /^I should see "([^"]*)" visible on the page$/
   */
  public function assertElementVisibleOnPage($text) {
    $element = $this->getSession()->getPage();
    $nodes = $element->findAll('css', 'li a, li button');
    foreach ($nodes as $node) {
      if ($node->getText() === $text) {
        if ($node->isVisible()) {
          return;
        }
        else {
          throw new \Exception("List item with text \"$text\" not visible.");
        }
      }
    }
    throw new ElementNotFoundException($this->getSession(), 'list item', 'text', $text);
  }

  /**
   * Checks, that form element with specified label is visible on page.
   *
   * @Then /^(?:|I )should see an? "(?P<label>[^"]*)" form element$/
   */
  public function assertFormElementOnPage($label) {
    $element = $this->getSession()->getPage();
    $nodes = $element->findAll('css', '.icarus-field label');
    foreach ($nodes as $node) {
      if ($node->getText() === $label) {
        if ($node->isVisible()) {
          return;
        }
        else {
          throw new \Exception("Form item with label \"$label\" not visible.");
        }
      }
    }
    throw new ElementNotFoundException($this->getSession(), 'form item', 'label', $label);
  }

  /**
   * Checks, that form element with specified label and type is visible on page.
   *
   * @Then /^(?:|I )should see an? "(?P<label>[^"]*)" (?P<type>[^"]*) form element$/
   */
  public function assertTypedFormElementOnPage($label, $type) {
    $container = $this->getSession()->getPage();
    $pattern = '/(^| )form-type-' . preg_quote($type) . '($| )/';
    $label_nodes = $container->findAll('css', 'body');

    foreach ($label_nodes as $label_node) {
      // Note: getText() will return an empty string when using Selenium2D. This
      // is ok since it will cause a failed step.
      if ($label_node->getText() === $label
        && preg_match($pattern, $label_node->getParent()->getAttribute('class'))
        && $label_node->isVisible()) {
        return;
      }
    }
    throw new ElementNotFoundException($this->getSession(), $type . ' form item', 'label', $label);
  }

  /**
   * Checks, that element with specified CSS is not visible on page.
   *
   * @Then /^(?:|I )should not see an? "(?P<label>[^"]*)" form element$/
   */
  public function assertFormElementNotOnPage($label) {
    $element = $this->getSession()->getPage();
    $nodes = $element->findAll('css', '.icarus-field label');
    foreach ($nodes as $node) {
      // Note: getText() will return an empty string when using Selenium2D. This
      // is ok since it will cause a failed step.
      if ($node->getText() === $label && $node->isVisible()) {
        throw new \Exception();
      }
    }
  }

  /**
   * Checks that form element with specific label/type isnt visible on page.
   *
   * @Then /^(?:|I )should not see an? "(?P<label>[^"]*)" (?P<type>[^"]*) form element$/
   */
  public function assertTypedFormElementNotOnPage($label, $type) {
    $container = $this->getSession()->getPage();
    $pattern = '/(^| )form-type-' . preg_quote($type) . '($| )/';
    $label_nodes = $container->findAll('css', '.icarus-field label');
    foreach ($label_nodes as $label_node) {
      // Note: getText() will return an empty string when using Selenium2D. This
      // is ok since it will cause a failed step.
      if ($label_node->getText() === $label
        && preg_match($pattern, $label_node->getParent()->getAttribute('class'))
        && $label_node->isVisible()) {
        throw new ElementNotFoundException($this->getSession(), $type . ' form item', 'label', $label);
      }
    }
  }

  /**
   * Press button with class.
   *
   * Example: When I press button with "class".
   *
   * @when /^(?:|I )press button with class "([^"]*)"$/
   */
  public function pressButtonWithClass($class) {
    $button = $this->getSession()->getPage()->find('css', $class);
    if (!empty($button)) {
      $button->click();
    }
    else {
      throw new Exception("Button with class $class not found");
    }
  }

  /**
   * That a single element is present.
   *
   * Example: Then I should see a single "element" element on the page.
   *
   * @Then I should see a single :element element on the page
   */
  public function assertSingleElementOnPage($element) {
    $container = $this->getSession()->getPage();
    $elements = $container->findAll('css', $element);

    if (empty($elements)) {
      throw new Exception("$element element not found");
    }

    $count = count($elements);
    if ($count > 1) {
      throw new Exception("Multiple $element elements found. $count in total.");
    }

    if ($count == 1) {
      return;
    }

    throw new Exception("Single $element element  not found");
  }

  /**
   * That a single element is present.
   *
   * Example: Then I should see an "element" element on the page containing "text" text: "debug" enabled.
   *
   * @Then I should see an :element element on the page containing :text text: :debug enabled
   */
  public function assertElementWitTextOnPage($element, $text, $debug = "false") {
    $container = $this->getSession()->getPage();
    $elements = $container->findAll('css', $element);

    if (empty($elements)) {
      throw new Exception("$element element not found");
    }

    $strip_chars = ["'", '’', '"'];

    foreach ($elements as $elem) {
      $elem_text = $elem->getText();
      $elem_text_clean = str_replace($strip_chars, '', $elem_text);
      if ($debug == 'true') {
        print_r("DEBUG: " . $elem_text_clean);
      }
      if (strpos($elem_text_clean, $text) !== FALSE) {
        return;
      }
    }

    throw new Exception("$element element with $text text not found." . PHP_EOL . "Found '$elem_text' instead");
  }

  /**
   * That a single element is present and visible.
   *
   * Example: Then I should see an "element" element on the page containing "text" text is visible: "debug" enabled.
   *
   * @Then I should see an :element element on the page containing :text text: is visible :debug enabled
   */
  public function assertElementWithTextVisibleOnPage($element, $text, $debug = "false") {
    $container = $this->getSession()->getPage();
    $elements = $container->findAll('css', $element);

    if (empty($elements)) {
      throw new Exception("$element element not found");
    }

    $strip_chars = ["'", '’', '"'];

    foreach ($elements as $elem) {
      $elem_text = $elem->getText();
      $elem_text_clean = str_replace($strip_chars, '', $elem_text);
      if ($debug == 'true') {
        print_r("DEBUG: " . $elem_text_clean);
      }
      if (strpos($elem_text_clean, $text) !== FALSE) {
        return;
      }
    }

    throw new Exception("$element element with $text text not found." . PHP_EOL . "Found '$elem_text' instead");
  }

  /**
   * That an element has an attribute with specific value.
   *
   * Example: Then I should see a "main" element with attribute "role" value "main".
   *
   * @Then I should see a :element element with attribute :attr value :value
   */
  public function assertElementWithAttributeValueOnPage($element, $attr, $value) {
    $container = $this->getSession()->getPage();
    $elements = $container->findAll('css', $element);

    if (empty($elements)) {
      throw new Exception("$element element not found");
    }

    foreach ($elements as $elem) {
      if ($elem->getAttribute($attr) == $value) {
        return;
      }
    }
    throw new Exception("$element element with attribute $attr value $value not found");
  }

  /**
   * That an element has an attribute containing a specific value.
   *
   * Example: Then I should see a "main" element with attribute "role" contains value "main".
   *
   * @Then I should see a :element element with attribute :attr contains value :value
   */
  public function assertElementWithAttributeContainsValueOnPage($element, $attr, $value) {
    $container = $this->getSession()->getPage();
    $elements = $container->findAll('css', $element);

    if (empty($elements)) {
      throw new Exception("$element element not found");
    }

    foreach ($elements as $elem) {
      if (empty($elem->getAttribute($attr))) {
        continue;
      }
      if (strpos($elem->getAttribute($attr), $value) !== FALSE) {
        return;
      }
    }
    throw new Exception("$element element with attribute $attr containing value $value not found");
  }

  /**
   * That an element has an attribute containing a specific value is visible.
   *
   * Example: Then I should see a "main" element with attribute "role" contains value "main" is visible
   *
   * @Then I should see a :element element with attribute :attr contains value :value is visible.
   */
  public function assertElementWithAttributeContainsValueOnPageIsVisible($element, $attr, $value) {
    $container = $this->getSession()->getPage();
    $elements = $container->findAll('css', $element);

    if (empty($elements)) {
      throw new Exception("$element element not not found");
    }

    foreach ($elements as $elem) {
      if (empty($elem->getAttribute($attr))) {
        continue;
      }
      if (strpos($elem->getAttribute($attr), $value) !== FALSE) {
        if ($elem->isVisible()) {
          return;
        }
      }
    }
    throw new Exception("$element element with attribute $attr containing value $value was found but is not visible");
  }

  /**
   * That an element with an attribute does not have a specific value.
   *
   * Example: Then I should not should see a "main" element with attribute "role" value "main".
   *
   * @Then I should not see a :element element with attribute :attr value :value
   */
  public function assertElementWithAttributeValueNotOnPage($element, $attr, $value) {
    $container = $this->getSession()->getPage();

    $elements = $container->findAll('css', $element);
    if (empty($elements)) {
      throw new Exception("$element element not found");
    }

    foreach ($elements as $elem) {
      if ($elem->getAttribute($attr) == $value) {
        throw new Exception("A '$element' tag containing $value in attribute '$attr' found");
      }
    }
  }

  /**
   * That an elements attribute value does not contain.
   *
   * Example: Then I should see a "main" element with attribute "role" value "main" does not contain "text".
   *
   * @Then I should see a :element element with attribute :attr value :value does not contain :text
   */
  public function assertElementWithAttributeValueOnPageDoesNotContainText($element, $attr, $value, $text) {
    $container = $this->getSession()->getPage();
    $elements = $container->findAll('css', $element);

    if (empty($elements)) {
      throw new Exception("$element element not found");
    }

    foreach ($elements as $elem) {
      if ($elem->getAttribute($attr) == $value) {
        if ($elem->getText() == $text) {
          throw new Exception("$element containing $text found");
        }
      }
    }
  }

  /**
   * That elements do not contain visible text.
   *
   * Example: I should not see a "span" element with text "foobar".
   *
   * @Then I should not see a :element element with text :text
   */
  public function theElementShouldNotContain($element, $text) {
    $page = $this->getSession()->getPage();
    $elements = $page->find('css', $element);

    if ($elements === NULL) {
      throw new \Exception(sprintf('No elements found with "%s"', $element));
    }

    foreach ($elements as $elem) {
      if (strpos($elem->getText(), $text) !== FALSE) {
        throw new \Exception(sprintf('Text "%s" was found in element "%s"', $text, $element));
      }
    }
  }

  /**
   * Test that the title and decription medata data is present on the page.
   *
   * Example: Then the title and description metadata should be set.
   *
   * @Then the title and description metadata should be set
   */
  public function assertTitleAndDescriptionMetadataOnPage() {
    $container = $this->getSession()->getPage();
    $meta_elements = $container->findAll('css', 'meta');
    $title_elements = $container->findAll('css', 'title');

    if (empty($meta_elements)) {
      throw new Exception("No meta elements found");
    }

    if (empty($title_elements)) {
      throw new Exception("Title element not found");
    }

    $meta_description_found = FALSE;
    foreach ($meta_elements as $elem) {
      $meta_name = $elem->getAttribute('name');
      $meta_content = $elem->getAttribute('content');

      if ($meta_name == 'description' && !empty($meta_content)) {
        $meta_description_found = TRUE;
      }
    }

    $meta_title_found = FALSE;
    foreach ($title_elements as $elem) {
      $title_value = $elem->getText();
      if (!empty($title_value)) {
        $meta_title_found = TRUE;
      }
    }

    if (!$meta_title_found) {
      throw new Exception("Meta title found, but it's empty");
    }

    // if (!$meta_description_found) {
    //   throw new Exception("Meta description not found or empty");
    // }

  }

  /**
   * Test that the pages are indexable and links are followable.
   *
   * Example: Then robots should not be blocking indexing and following.
   *
   * @Then robots should not be blocking indexing and link following
   */
  public function assertPageIsIndexableAndLinksFollowable() {
    $container = $this->getSession()->getPage();
    $elements = $container->findAll('css', 'meta');

    if (empty($elements)) {
      throw new Exception("Meta elements not found");
    }

    foreach ($elements as $elem) {
      $meta_name = $elem->getAttribute('name');
      $meta_content = $elem->getAttribute('content');

      // Check for <meta name="robots" content="noindex, nofollow">.
      if ($meta_name == 'robots' && !empty($meta_content)) {
        if (strpos($meta_content, 'noindex')) {
          throw new Exception("Robots noindex meta tag found");
        }
        if (strpos($meta_content, 'nofollow')) {
          throw new Exception("Robots nofollow meta tag found");
        }
      }
    }
  }

  /**
   * Test that the pages are not indexable and links are not followable.
   *
   * Example: Then robots should be blocking indexing and following.
   *
   * @Then robots should be blocking indexing and link following
   */
  public function assertPageIsNotIndexableAndLinksFollowable() {
    $container = $this->getSession()->getPage();
    $elements = $container->findAll('css', 'meta');

    if (empty($elements)) {
      throw new Exception("Meta elements not found");
    }

    $noindex = FALSE;
    $nofollow = FALSE;

    foreach ($elements as $elem) {
      $meta_name = $elem->getAttribute('name');
      $meta_content = $elem->getAttribute('content');


      // Check for <meta name="robots" content="noindex, nofollow">.
      if ($meta_name == 'robots' && !empty($meta_content)) {
        if ($meta_content == 'noindex, nofollow') {
          $noindex = TRUE;
          $nofollow = TRUE;
          break;
        }

        // See if we can identify which one is missing.
        if (strpos($meta_content, 'noindex')) {
          $noindex = TRUE;
        }
        if (strpos($meta_content, 'nofollow')) {
          $nofollow = TRUE;
        }
      }

      if ($noindex && $nofollow) {
        break;
      }
    }

    if (empty($noindex)) {
      throw new Exception("Robots noindex meta tag not found");
    }
    if (empty($nofollow)) {
      throw new Exception("Robots nofollow meta tag not found");
    }
  }

  /**
   * That a link does not contain rel nofollow, unless its a pager.
   *
   * Example: Then I should not see rel nofollow on any links.
   *
   * @Then I should not see rel nofollow on any links
   */
  public function assertLinksDoNotHaveNoFollow() {
    $container = $this->getSession()->getPage();

    // Pager title value to exclude in check.
    $pager_title = 'Go to next page';

    // First check a tags.
    $tag = 'a';
    $elements = $container->findAll('css', $tag);
    $attr = 'rel';
    $value = 'nofollow';

    if (empty($elements)) {
      throw new Exception("$tag element not found");
    }

    foreach ($elements as $elem) {
      if ($elem->getAttribute($attr) == $value && $elem->getAttribute('title') != $pager_title) {
        throw new Exception("A '$tag' tag containing $value in attribute '$attr' found");
      }
    }

    // Next check buttons.
    $tag = 'button';
    $elements = $container->findAll('css', $tag);

    if (empty($elements)) {
      throw new Exception("$tag element not found");
    }

    foreach ($elements as $elem) {
      if ($elem->getAttribute($attr) == $value && $elem->getAttribute('title') != $pager_title) {
        throw new Exception("A '$tag' tag containing $value in attribute '$attr' found");
      }
    }
  }

  /**
   * Test that the images all have alt text.
   *
   * Example: Then I should see images with populated alt tags.
   *
   * @Then I should see images with populated alt tags
   */
  public function iShouldSeeImagesWithAltTags() {
    $container = $this->getSession()->getPage();
    $elements = $container->findAll('css', 'img');
    $errors = [];
    foreach ($elements as $image) {
      $alt = $image->getAttribute('alt');
      $src = $image->getAttribute('src');
      if (empty($alt)) {
          $errors[] = "Image with src '$src' does not have an alt tag defined";
      }
    }

    if (!empty($errors)) {
      $count = count($errors);
      print_r($errors);
      throw new Exception("$count image(s) without alt tag found");
    }
  }

  /**
   * Test that the page has specific shema elements.
   *
   * Example: Then I should see the "BreadcrumbList" schema in the page head.
   *
   * @Then I should see the :schema_name schema in the page head
   */
  public function theGivenSchemaMetadataShouldBePresent($schema_name) {
    $container = $this->getSession()->getPage();
    $metadata = $container->findAll('xpath', '//script[@type="application/ld+json"]');
    $found = FALSE;

    foreach ($metadata as $script) {
      $content = $script->getHtml();
      $json = json_decode($content, TRUE);

      if ($schema_name == 'review') {
        if (!empty($json[$schema_name]) && !empty($json['aggregateRating'])) {
          if (!empty($json['aggregateRating']['ratingCount']) && !empty($json['review'])) {
            $found = TRUE;
            break;
          }
        }
      }

      if ($schema_name == 'LegalService') {
        if ((!empty($json['@graph']) && $json['@graph'][1]['additionalType']) ||
            (!empty($json['mainEntity']) && !empty($json['mainEntity']['additionalType']) && $json['mainEntity']['additionalType'] == $schema_name)) {
          $found = TRUE;
          break;
        }
      }

      if ($schema_name == 'Product') {
        if (!empty($json['product']) && $json['product']['@type'] == $schema_name) {
          $found = TRUE;
          break;
        }
      }

      // Verify Practice Area schema.
      if ($schema_name == 'WebPage' && !empty($json['@type']) && $json['@type'] === 'WebPage') {
        // PA Hub schema
        if (isset($json['breadcrumb']) && isset($json['mainEntity']) && isset($json['publisher']) &&
            isset($json['mentions']) && isset($json['isPartOf'])) {
            $found = TRUE;
            break;
        }
        // PA Local Office schema.
        if (isset($json['mainEntity']) && isset($json['publisher']) && isset($json['breadcrumb']) &&
            isset($json['potentialAction']) && isset($json['mentions'])) {
              // Verify that 'hasMap' and 'ratingValue' are present and valid.
              if (!empty($json['mentions']['hasMap']) &&
                isset($json['mentions']['aggregateRating']['ratingValue']) &&
                !empty($json['mentions']['aggregateRating']['ratingValue'])) {
                $found = TRUE;
                break;
              }
        }
        // PA Local State schema.
        if (isset($json['mainEntity']) && isset($json['review']) && isset($json['brand']) &&
            isset($json['publisher']) && isset($json['isPartOf'])) {
            // Verify that 'subOrganization' is present.
            if (!empty($json['mainEntity']['subOrganization'])) {
              $found = TRUE;
              break;
            }
        }
        // PA Spoke schema.
        if (isset($json['mainEntity']) && isset($json['publisher']) && isset($json['isPartOf']) &&
            isset($json['mentions'])) {
          $found = TRUE;
          break;
        }
      }

      if (isset($json[0])) {
        foreach ($json as $item) {
          if ((isset($item['@type']) && $item['@type'] === $schema_name) ||
              (isset($item['additionalType']) && $item['additionalType'] === $schema_name)) {
            $found = TRUE;
            break;
          }
        }
      }

      // Verify generic schema.
      elseif ((isset($json['@type']) && $json['@type'] === $schema_name)) {
        $found = TRUE;
        break;
      }
    }

    if (!$found) {
      throw new Exception($schema_name . ' schema metadata not found');
    }
  }


  /**
   * Scroll an element into view.
   *
   * @param string $selector
   *   Allowed selectors: #id, .className, //xpath.
   *
   * @throws \Exception
   *
   * @When I scroll :selector into view
   */
  public function scrollIntoView($selector) {
    $locator = substr($selector, 0, 1);

    switch ($locator) {
      // Query selector.
      case '$':
        $selector = substr($selector, 1);
        $function = <<<JS
          (function(){
            var elem = document.querySelector("$selector");
            elem.scrollIntoView(false);
          })()
          JS;
        break;

      // XPath selector.
      case '/':
        $function = <<<JS
          (function(){
            var elem = document.evaluate("$selector", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
            elem.scrollIntoView(false);
          })()
          JS;
        break;

      // ID selector.
      case '#':
        $function = 'window.jQuery("'. $selector . '")[0].scrollIntoView();';
        break;

      // Class selector.
      case '.':
        $function = 'window.jQuery("'. $selector . '")[0].scrollIntoView();';
        break;

      default:
        throw new \Exception(__METHOD__ . ' Couldn\'t find selector: ' . $selector . ' - Allowed selectors: #id, .className, //xpath');
    }

    try {
      $this->getSession()->executeScript($function);
    }
    catch (Exception $e) {
      throw new \Exception(__METHOD__ . ' failed');
    }
  }

  /**
   * Checks for the attorney master list.
   *
   * @Then /^I should see the attorney master list$/
   */
  public function iSeeAttorneyMasterList() {
    $this->getSession()->wait(15000);
    $js = <<<JS
      function() {if (sessionStorage.getItem("citrusAttorneyMasterList") != undefined) { return "PASS"; } else { return "FAIL"; }}
    JS;
    $results = $this->getSession()->evaluateScript("$js");
    if ($results == "FAIL") {
      throw new Exception('citrusAttorneyMasterList object not found');
    }
  }

  /**
   * Checks for elements within a given iframe.
   *
   * Example: Then I should see a "div" element with attribute "id" value "mass-tort-case" in iframe id/class "abassadors-form-frame".
   *
   * @Then I should see a :element element with attribute :attr with value :value in iframe named :name
   */
  public function iShouldSeeAElementWithAttributeWithValueInIframeIdClass($element, $attr, $value, $name) {

    $this->getSession()->switchToIFrame($name);
    $iframe_element = $this->getSession()->getPage();

    if (empty($iframe_element)) {
      throw new Exception("Iframe with name $name not found");
    }

    $elements = $iframe_element->findAll('css', $element);

    foreach ($elements as $elem) {
      if ($elem->getAttribute($attr) == $value) {
        return;
      }
    }
    throw new Exception("$element element with attribute $attr value $value not found within iframe");
  }

  /**
   * @Then I should see :num links in the :class element
   */
  public function iShouldSeeLinksInTheElement($num, $class) {
    $session = $this->getSession();
    $page = $session->getPage();

    $element = $page->find('css', '.' . $class);

    if ($element === NULL) {
        throw new Exception('Could not find element with class name: "' . $class . '"');
    }

    $links = $element->findAll('css', 'a');

    if (count($links) < intval($num)) {
      throw new Exception('Expected ' . $num . ' links, but found ' . count($links));
    }
  }

  /**
   * Resizes the browser to specified width and reloads.
   *
   * Example: Then I resize browser window to narrow
   *
   * @Then I resize browser window to narrow
   */
  public function resizeToNarrowWindow() {
    $this->getSession()->resizeWindow(415, 900, 'current');
    $this->getSession()->reload();
  }

  /**
   * Resizes the browser to specified width and reloads.
   *
   * Example: Then I resize browser window to desktop
   *
   * @Then I resize browser window to desktop
   */
  public function resizeToDesktopWindow() {
    $this->getSession()->resizeWindow(2000, 1200, 'current');
    $this->getSession()->reload();
  }

}
