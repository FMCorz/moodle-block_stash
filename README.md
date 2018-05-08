Block Stash ![GitHub tag](https://img.shields.io/github/tag/branchup/moodle-filter_shortcodesblock_stash.svg) ![Travis branch](https://img.shields.io/travis/branchup/moodle-block_stash/master.svg)
===========

Engage your students! Gamify your courses by hiding objects for your students to find.

Features
--------

- Create any object you like
- Hide objects throughout your course in any possible location
- Set objects to automatically re-appear after a delay to boost engagement
- Unlock access to activities based on the objects found (requires plugin [availability_stash](https://moodle.org/plugins/availability_stash))
- Trade by exchanging items for different items (requires plugin [filter plugin](https://moodle.org/plugins/filter_stash))

Requirements
------------

Moodle 3.1 or greater.

Installation
------------

Simply install the plugin and add the block to a course page.

_Please read the [Recommended plugins](#recommended-plugins) section._

Getting started
---------------

### Creating an item

1. Create a new item
2. Create a new location for that item
3. Copy the code snippet for that location
4. Directly paste the code in the HTML view of your editor

When viewing the content the object will now appear.
Note that teachers cannot pick up the objects, for them they will always re-appear.

### Creating a trade (item exchange)

1. Create at least two items (see creating an item above)
2. Click the create trade widget button.
3. Add items to gain on the left side and items to lose on the right.
4. Once saved click on the trade name and copy the code snippet.
5. Directly paste the code snippet into any location that has an editor.

Recommended plugins
-------------------

### Shortcodes filter

This [filter plugin](https://github.com/branchup/moodle-filter_shortcodes) makes it easier and more reliable to add the items to your course content. We very highly recommend you to use it. This is a requirement to use the trading feature.

### Stash availability

This [availability plugin](https://moodle.org/plugins/availability_stash) allows to restrict the access to activity modules and resources based on the objects collected by a student.

License
-------

Licensed under the [GNU GPL License](http://www.gnu.org/copyleft/gpl.html).
