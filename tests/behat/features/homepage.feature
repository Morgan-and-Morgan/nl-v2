@dev @prod @verify_homepage
Feature: Visibility of the home page
  In order to have confidence that build tools works
  I want to verify I can visit a valid home page on Pantheon

  Scenario: Verify the homepage
    When I am on the homepage
    Then I should be on the homepage
    And the response status code should be 200
