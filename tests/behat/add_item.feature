@block @block_stash
Feature: Teachers can add items to the stash block
  In order to add items to a stash
  As a teacher
  I need to add the block stash and visit the settings.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1 | 0 | topics |
    And the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | t1 |
      | student1 | Student | 1 | student1@example.com | s1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Stash" block

  @javascript
  Scenario: Add, Edit and delete an item in the stash
    When I follow "Setup"
    And I press "Add an item"
    And I set the field "Item name" to "Coin"
    And I upload "blocks/stash/tests/fixtures/coin.png" file to "Image" filemanager
    And I press "Save and next"
    And I set the field "Location" to "In the page"
    Given I press "Save changes"
    And the following should exist in the "tablewithitems" table:
      | Item name |
      | Coin      |
    And I follow "In the page"
    And I should see "We recommend that you install"
    And I press "Close"
    And I follow "Edit item 'Coin'"
    And I set the field "Item name" to "Gold circle"
    And I press "Save changes"
    And the following should exist in the "tablewithitems" table:
      | Item name   |
      | Gold circle |
    And I follow "Delete Gold circle"
    And I press "Yes"
    Then I should see "Nothing to display"
