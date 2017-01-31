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

// MoodleQuickForm::registerElementType('block_stash_integer', __DIR__ . '/integer.php', 'block_stash_form_integer');

class trade extends persistent {

    protected static $persistentclass = 'block_stash\\trade';

    protected static $fieldstoremove = array('save', 'submitbutton');

    protected static $foreignfields = array('itemid', 'quantity', 'gainloss', 'saveandnext');

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        global $PAGE, $OUTPUT, $CFG;

        $mform = $this->_form;
        $stash = $this->_customdata['stash'];
        $manager = $this->_customdata['manager'];
        $competency = $this->get_persistent();

        $mform->addElement('header', 'generalhdr', get_string('general'));

        // Stash ID.
        $mform->addElement('hidden', 'stashid');
        $mform->setType('stashid', PARAM_INT);
        $mform->setConstant('stashid', $stash->get_id());

        // Name.
        $mform->addElement('text', 'name', get_string('tradename', 'block_stash'), 'maxlength="255"');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'tradename', 'block_stash');

        // Items.
        $mform->addElement('header', 'tradeitems', get_string('tradeitems', 'block_stash'));

        $options = [];
        $items = $manager->get_items();
        foreach ($items as $key => $item) {
            $options[$item->get_id()] = $item->get_name();
        }
        
        $mform->addElement('select', 'itemid', get_string('item', 'block_stash'), $options);
        $mform->addElement('text', 'quantity', get_string('quantity', 'block_stash'));
        $mform->setType('quantity', PARAM_INT);
        $options = [1 => get_string('gain', 'block_stash'), 0 => get_string('loss', 'block_stash')];
        $mform->addElement('select', 'gainloss', get_string('gainloss', 'block_stash'), $options);

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
