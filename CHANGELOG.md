Block Stash
===========

Version 1.3.1 (10th May 2019)
-----------------------------

* Fix typo in language string identifier

Version 1.3.0 (9th May 2019)
----------------------------

* Implement privacy API (GDPR compliance)
* Drop support for Moodle 2.9 and 3.0
* Hash code size was reduced to 6 characters
* Support snippets from [filter_shortcodes](https://github.com/branchup/moodle-filter_shortcodes)
* Drop support and deprecate filter_stash
* Slightly improved styling across themes and versions
* Minor improvements and bug fixing

Version 1.2.3 (30th August 2017)
--------------------------------
* Added the ability to edit the block title.

Version 1.2.2 (10th August 2017)
--------------------------------
* Issues with the persistence in Moodle 3.2 fixed.

Version 1.2.1 (9th August 2017)
-------------------------------
* Fixed the error on the trade creation form. We can't export for template the help icon in earlier versions.

Version 1.2.0 (9th August 2017)
-------------------------------
* Added the trading system. This improvement requires the filter_stash plugin to be installed to work. Teachers can create a trade widget that will allow students to swap or exchange items they currently have for different items.
* Backup and restore should work properly now. If the filter was used and the snippet was small enough then the encoding would not work.
* Basic fixes to deprecated libraries calls.

Version 1.1.0 (26th August 2016)
--------------------------------
* Support for filter_stash - filter_stash allows the copying of code to be simplified to a very small string which can then be copied straight into editors. The need for burrowing to the raw HTML is no longer needed.
* Detail about each item can now be added. When creating an item you have an extra field (editor) to put additional detail about an item. When a student clicks on an item they have acquired a dialogue will pop up with the full information about the item.
* Clicking on an item that is visible on the course page will now update to the block without the need for a page refresh.
* A report page allows teachers to get a peek at their students' stash.
* Various user interface improvements

Version 1.0.0 (21st July 2016)
------------------------------
Various bug fixes.
