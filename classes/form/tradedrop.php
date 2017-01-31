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
 * Trade drop form.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_stash\form;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use stdClass;
use MoodleQuickForm;

// MoodleQuickForm::registerElementType('block_stash_integer', __DIR__ . '/integer.php', 'block_stash_form_integer');

/**
 * Trade drop form class.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tradedrop extends persistent {

    protected static $persistentclass = 'block_stash\\tradedrop';

    protected static $fieldstoremove = array('save', 'submitbutton');

    // protected static $foreignfields = array('saveandnext');

    public function definition() {
        global $PAGE, $OUTPUT;

        $mform = $this->_form;
        $manager = $this->_customdata['manager'];
        $trade = $this->_customdata['trade'];
        $context = $manager->get_context();
        $tradename = $trade ? format_string($trade->get_name(), null, ['context' => $context]) : null;
        $tradedrop = $this->get_persistent();

        $mform->addElement('header', 'generalhdr', get_string('general'));

        // Item ID.
        if ($trade) {
            $mform->addElement('hidden', 'tradeid');
            $mform->setType('tradeid', PARAM_INT);
            $mform->setConstant('tradeid', $trade->get_id());
            $mform->addElement('static', '', get_string('trade', 'block_stash'), $tradename);

        } else {
            $trades = $manager->get_trades();
            $options = [];
            foreach ($trades as $stashitem) {
                $options[$stashitem->get_id()] = format_string($stashitem->get_name(), null, ['context' => $context]);
            }
            $mform->addElement('select', 'tradeid', get_string('trade', 'block_stash'), $options);
        }

        // Hash code.
        $mform->addElement('hidden', 'hashcode');
        $mform->setType('hashcode', PARAM_ALPHANUM);
        $mform->setConstant('hashcode', $tradedrop->get_hashcode());

        // Name.
        $mform->addElement('text', 'name', get_string('dropname', 'block_stash'),
            'maxlength="100" placeholder="' . s(get_string('eginthecastle', 'block_stash')) . '"');
        $mform->setType('name', PARAM_NOTAGS);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');
        $mform->addHelpButton('name', 'dropname', 'block_stash');

        // Buttons.
        $buttonarray = [];
        if (!$this->get_persistent()->get_id()) {
            // Only for new items.
            // $buttonarray[] = &$mform->createElement('submit', 'saveandnext', get_string('saveandnext', 'block_stash'),
            //     ['class' => 'form-submit']);
            $buttonarray[] = &$mform->createElement('submit', 'save', get_string('savechanges', 'block_stash'));
        } else {
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges', 'block_stash'));
        }

        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');    }

}
