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

MoodleQuickForm::registerElementType('block_stash_integer', __DIR__ . '/integer.php', 'block_stash_form_integer');

class item extends persistent {

    protected static $persistentclass = 'block_stash\\item';

    protected static $fieldstoremove = array('save', 'submitbutton');

    protected static $foreignfields = array('image', 'saveandnext', 'detail_editor');

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        global $PAGE, $OUTPUT, $CFG;

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
        $mform->addHelpButton('name', 'itemname', 'block_stash');

        // Max number of items.
        // $mform->addElement('block_stash_integer', 'maxnumber', get_string('maxnumber', 'block_stash'), ['style' => 'width: 3em;']);
        // $mform->setType('maxnumber', PARAM_INT);

        // Image.
        $mform->addElement('filemanager', 'image', get_string('itemimage', 'block_stash'), array(), $this->_customdata['fileareaoptions']);
        $mform->addRule('image', null, 'required', null, 'client');
        $mform->addHelpButton('image', 'itemimage', 'block_stash');

        // Detail.
        $mform->addElement('editor', 'detail_editor', get_string('itemdetail', 'block_stash'), array('rows' => 10),
                $this->_customdata['editoroptions']);
        $mform->setType('detail_editor', PARAM_RAW);
        $mform->addHelpButton('detail_editor', 'itemdetail', 'block_stash');

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
