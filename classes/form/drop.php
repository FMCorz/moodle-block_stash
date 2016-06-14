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
 * Item drop form.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_stash\form;
defined('MOODLE_INTERNAL') || die();

use stdClass;

/**
 * Item drop form class.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class drop extends persistent {

    protected static $persistentclass = 'block_stash\\drop';

    public function definition() {
        global $PAGE, $OUTPUT;

        $mform = $this->_form;
        $item = $this->_customdata['item'];
        $drop = $this->get_persistent();

        $mform->addElement('header', 'generalhdr', get_string('general'));

        // Item ID.
        $mform->addElement('hidden', 'itemid');
        $mform->setType('itemid', PARAM_INT);
        $mform->setConstant('itemid', $item->get_id());

        // Hash code.
        $mform->addElement('hidden', 'hashcode');
        $mform->setType('hashcode', PARAM_ALPHANUM);
        $mform->setConstant('hashcode', $drop->get_hashcode());

        // Name.
        $mform->addElement('text', 'name', 'Name', 'maxlength="100"');
        $mform->setType('name', PARAM_NOTAGS);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');

        // Max pickup.
        $mform->addElement('text', 'maxpickup', 'Maximum pickup', 'maxlength="10" size="5"');
        $mform->setType('maxpickup', PARAM_INT);
        $mform->addRule('maxpickup', null, 'required', null, 'client');
        $mform->addRule('maxpickup', get_string('maximumchars', '', 10), 'maxlength', 10, 'client');

        // Pickup interval.
        $mform->addElement('duration', 'pickupinterval', 'Pickup interval');
        $mform->setType('pickupinterval', PARAM_INT);
        $mform->disabledIf('pickupinterval', 'maxpickup', 'eq', 1);

        $this->add_action_buttons(true, get_string('savechanges', 'tool_lp'));
    }

}
