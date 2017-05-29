<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings.
 *
 * @package   block_stash
 * @copyright 2016 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['additem'] = 'Add an item';
$string['addnewdrop'] = 'Add new location';
$string['addnewtradeitem'] = 'Add new item to this trade';
$string['addtradeitem'] = 'Add a trade item';
$string['addtoinventory'] = 'Add an item to the inventory.';
$string['addtrade'] = 'Add a trade widget';
$string['aftercreatinglocationhelp'] = 'Once you have created the item and the location, you need to add a code snippet to your course for the item to be displayed. After customising how the item will be displayed to your students, copy the snippet below and paste it in your content, in the description of an assignment for instance.';
$string['appearance'] = 'Appearance';
$string['backtostart'] = 'Back to the main screen';
$string['buttontext'] = 'Button text';
$string['copypaste'] = 'Copy and paste this into an editor in different activities around your course.';
$string['cost'] = 'Cost';
$string['deletedrop'] = 'Delete {$a}';
$string['deleteitem'] = 'Delete {$a}';
$string['dropa'] = 'Location \'{$a}\'';
$string['dropname'] = 'Location';
$string['dropname_help'] = 'The name of the location is only useful for you to organise them, it will not be displayed to the students.';
$string['drops'] = 'Locations';
$string['drops_help'] = '
<p>Locations are places where your items are in the <em>virtual world</em>. Without a <em>location</em> an item cannot be picked up by a student.</p>
<p>Locations come with a few options, including the number of times a single student can pick them up, and how often they reappear after being picked up.</p>
<p>For instance, if your students need a <em>key item</em> to access an activity, you will most likely set it so that your students can only pick it up once in that location.</p>
<p>But if they need <em>5 coins</em> to access another one, you may set this coin to reappear each day to encourage them to visit the course everyday.</p>
<p>Note that items do not magically appear in your course, you will have to add a special code to your content in order for the item to be displayed.</p>
';
$string['dropslist'] = 'List of locations';
$string['dropsnippet'] = 'Snippet for \'{$a}\'';
$string['dropsummary'] = 'Location summary';
$string['edit'] = 'Edit'; // Should be replaced with the pix icon.
$string['editdrop'] = 'Edit location \'{$a}\'';
$string['edititem'] = 'Edit item \'{$a}\'';
$string['edittrade'] = 'Edit trade widget \'{$a}\'';
$string['edittradeitem'] = 'Edit trade item \'{$a}\'';
$string['eginthecastle'] = 'E.g. In the castle';
$string['eventitemacquired'] = 'An item was acquired.';
$string['filterstashnotactive'] = 'The filter plugin is installed but not yet enabled for this course. Visit <a href="{$a->activeurl}" target="_blank">this page</a> to enable it for this course.';
$string['filterstashnotenabled'] = 'The filter plugin is installed but not yet <a href="{$a->enableurl}" target="_blank">enabled</a>.';
$string['filterstashnotinstalled'] = 'We recommend that you install and enable the <a href="{$a->installurl}" target="_blank">filter plugin for Stash</a>. It makes it easier and more reliable to use the snippets. It also enables trading.';
$string['filterstashwrongversion'] = 'The filter plugin that you have installed is an early version and does not work with trading. Please visit <a href="{$a}" target="_blank">this page</a> to get the latest version.';
$string['gain'] = "Gain";
$string['gainloss'] = "Gain or lose";
$string['gaintitle'] = "Gain title";
$string['gaintitle_help'] = "Title for the column of items the user will acquire in this trade.";
$string['image'] = 'Image';
$string['imageandbutton'] = 'Image and button';
$string['item'] = 'Item';
$string['itemdetail'] = 'Details';
$string['itemdetail_help'] = 'Details about the item.';
$string['itemimage'] = 'Image';
$string['itemimage_help'] = 'This image will be used to display the item. The recommended size is 100x100 pixels.';
$string['itemname'] = 'Item name';
$string['itemname_help'] = 'The name of the item, this will be displayed to the students.';
$string['items'] = 'Items';
$string['itemslist'] = 'List of items';
$string['locations'] = 'Locations';
$string['loss'] = 'Loss';
$string['losstitle'] = 'Loss title';
$string['losstitle_help'] = 'Title for the column of items the user will surrender in this trade.';
$string['maxnumber'] = 'Maximum collectible';
$string['maxpickup'] = 'Supplies';
$string['maxpickup_help'] = 'The number of times the item can be picked up by each students in this location. For instance, if you set this to \'1\' the item will only be available once per student. If you set it to \'5\', each student can acquire the item five times in this location. A value different than \'1\' is better used in combination with the \'Collection interval\'.';
$string['navdrops'] = 'Locations';
$string['navinventory'] = 'Stash items';
$string['navitems'] = 'Items';
$string['navreport'] = 'Report';
$string['navtrade'] = 'Trade';
$string['none'] = 'None';
$string['number'] = 'Number';
$string['pickupa'] = 'Pick up \'{$a}\'';
$string['pickupinterval'] = 'Collection interval';
$string['pickupinterval_help'] = 'This defines the time required for the item to re-appear to students who already picked this item up. For instance, if you created an item \'cake\' you could set its collection interval to 24 hours to simulate the time it takes for the baker to bake another one. It is important to note that students are not affected by the pickups of other students. This setting has no effect when \'Supplies\' is set to \'1\'.';
$string['pluginname'] = 'Stash';
$string['quantity'] = 'Quantity';
$string['reallydeletedrop'] = 'Are you sure you want to delete this location?';
$string['reallyresetstashof'] = 'Are you sure you want to completely reset the stash of {$a}?';
$string['resetstashof'] = 'Reset the stash of {$a}';
$string['saveandnext'] = 'Save and next';
$string['savechanges'] = 'Save changes';
$string['stashdisabled'] = 'The stash is not enabled. Was the block added to the course?';
$string['reallydeleteitem'] = 'Are you sure you want to delete this item?';
$string['report'] = 'Report';
$string['setup'] = 'Setup';
$string['settings'] = 'Settings';
$string['snippet'] = 'Snippet';
$string['stash'] = 'Stash';
$string['stash:acquireitems'] = 'User is able to acquire items';
$string['stash:addinstance'] = 'Add a the block to a page';
$string['stash:view'] = 'View the stash and its content';
$string['text'] = 'Text';
$string['trade'] = 'Trade';
$string['tradeitem'] = 'Trade item';
$string['tradeitems'] = 'Trade items';
$string['tradelist'] = 'List of trade widgets';
$string['tradename'] = 'Trade name';
$string['tradename_help'] = 'The name of the trade widget, this may be displayed to the students.';
$string['thedrophasbeendeleted'] = 'The location \'{$a}\' has been deleted';
$string['theitemhasbeendeleted'] = 'The item \'{$a}\' has been deleted';
$string['thestashofhasbeenreset'] = 'The stash of {$a} has been reset';
$string['thetradehasbeendeleted'] = 'The trade widget \'{$a}\' has been deleted';
$string['unlimited'] = 'Unlimited';
$string['whataredrops'] = 'What are locations?';
$string['whatisadrophelp'] = 'A location is a place where you intend to display your item.';
$string['whatisatradedrophelp'] = 'A location is a place where you intend to display your trade widget.';
$string['whatisthisthing'] = 'What is this thing? I am certain you can find a use for it!';
$string['whatsthis'] = 'What\'s this?';
$string['whatsnext'] = 'What\'s next?';
$string['yourinventoryisempty'] = 'Your inventory is empty.';
