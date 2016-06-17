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

MoodleQuickForm::registerElementType('block_stash_integer', __DIR__ . '/integer.php', 'block_stash\\form\\integer');

class item extends persistent {

    protected static $persistentclass = 'block_stash\\item';

    protected static $foreignfields = array('image');

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        global $PAGE, $OUTPUT;

        $mform = $this->_form;
        $stash = $this->_customdata['stash'];
        $competency = $this->get_persistent();

        $mform->addElement('header', 'generalhdr', get_string('general'));

        // Stash ID.
        $mform->addElement('hidden', 'stashid');
        $mform->setType('stashid', PARAM_INT);
        $mform->setConstant('stashid', $stash->get_id());

        // Name.
        $mform->addElement('text', 'name', get_string('itemname', 'block_stash'), 'maxlength="255"');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Max number of items.
        // $mform->addElement('block_stash_integer', 'maxnumber', get_string('maxnumber', 'block_stash'), ['style' => 'width: 3em;']);
        // $mform->setType('maxnumber', PARAM_INT);

        // Image.
        $mform->addElement('filemanager', 'image', 'Image', array(), $this->_customdata['fileareaoptions']);
        $mform->addRule('image', null, 'required', null, 'client');

        $this->add_action_buttons(true, get_string('savechanges', 'tool_lp'));
    }

}
