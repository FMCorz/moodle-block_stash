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


namespace block_stash\form;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use stdClass;
use MoodleQuickForm;

class trade extends persistent {

    protected static $persistentclass = 'block_stash\\trade';

    protected static $fieldstoremove = array('save', 'submitbutton');

    protected static $foreignfields = array('saveandnext');

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        global $PAGE, $OUTPUT, $CFG;

        $mform = $this->_form;
        $stash = $this->_customdata['stash'];
        $manager = $this->_customdata['manager'];
        $trade = $this->get_persistent();

        $mform->addElement('header', 'generalhdr', get_string('general'));

        // Stash ID.
        $mform->addElement('hidden', 'stashid');
        $mform->setType('stashid', PARAM_INT);
        $mform->setConstant('stashid', $stash->get_id());

        // Hash code.
        $mform->addElement('hidden', 'hashcode');
        $mform->setType('hashcode', PARAM_ALPHANUM);
        $mform->setConstant('hashcode', $trade->get_hashcode());

        // Name.
        $mform->addElement('text', 'name', get_string('tradename', 'block_stash'), 'maxlength="255"');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'tradename', 'block_stash');

        // Loss title.
        $mform->addElement('text', 'losstitle', get_string('losstitle', 'block_stash'), 'maxlength="255",
                placeholder="' . s(get_string('cost', 'block_stash')) . '"');
        $mform->setType('losstitle', PARAM_TEXT);
        $mform->setDefault('losstitle', get_string('cost', 'block_stash'));
        $mform->addRule('losstitle', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('losstitle', 'losstitle', 'block_stash');

        // Gain title.
        $mform->addElement('text', 'gaintitle', get_string('gaintitle', 'block_stash'), 'maxlength="255",
                placeholder="' . s(get_string('item', 'block_stash')) . '"');
        $mform->setType('gaintitle', PARAM_TEXT);
        $mform->setDefault('gaintitle', get_string('item', 'block_stash'));
        $mform->addRule('gaintitle', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('gaintitle', 'gaintitle', 'block_stash');

        // Buttons.
        $buttonarray = [];
        if (!$this->get_persistent()->get_id()) {
            // Only for new items.
            $buttonarray[] = &$mform->createElement('submit', 'saveandnext', get_string('saveandnext', 'block_stash'),
                ['class' => 'form-submit']);
            $buttonarray[] = &$mform->createElement('submit', 'save', get_string('savechanges', 'block_stash'));
        } else {
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges', 'block_stash'));
        }

        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

}
