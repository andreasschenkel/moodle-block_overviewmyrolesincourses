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
 * Block edit form class for the block_overviewmyrolesincourses plugin.
 *
 * @package   block_overviewmyrolesincourses
 * @copyright 2022, Andreas Schenkel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_overviewmyrolesincourses_edit_form extends block_edit_form {
    /**
     * Form for editing overviewmyrolesincourses block instances.
     *
     * @param MoodleQuickForm $mform the form to configure the block
     * @return void
     * @throws coding_exception
     */
    protected function specific_definition($mform) {
        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        $name = get_string('showpast', 'block_overviewmyrolesincourses');
        $mform->addElement('advcheckbox', 'config_showpast', $name, '', ['group' => 1], [0, 1]);
        $mform->setDefault('config_showpast', 0);

        $name = get_string('showinprogress', 'block_overviewmyrolesincourses');
        $mform->addElement('advcheckbox', 'config_showinprogress', $name, '', ['group' => 2], [0, 1]);
        $mform->setDefault('config_showinprogress', 0);

        $name = get_string('showfuture', 'block_overviewmyrolesincourses');
        $mform->addElement('advcheckbox', 'config_showfuture', $name, '', ['group' => 3], [0, 1]);
        $mform->setDefault('config_showfuture', 0);

        $name = get_string('onlyfavourite', 'block_overviewmyrolesincourses');
        $mform->addElement('advcheckbox', 'config_onlyfavourite', $name, '', ['group' => 4], [0, 1]);
        $mform->setDefault('config_onlyfavourite', 0);

        $name = get_string('foldonstart', 'block_overviewmyrolesincourses');
        $mform->addElement('advcheckbox', 'config_foldonstart', $name, '', ['group' => 5], [0, 1]);
        $mform->setDefault('config_foldonstart', 0);
    }
}
