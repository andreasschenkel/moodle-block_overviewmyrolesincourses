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
 * Settings for this block plugin.
 *
 * @package    block_overviewmyrolesincourses
 * @copyright  Andreas Schenkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$settings = new admin_settingpage('block_overviewmyrolesincourses', get_string('pluginname', 'block_overviewmyrolesincourses'));
if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configcheckbox(
        'block_overviewmyrolesincourses/isactiv',
        get_string('isactiv', 'block_overviewmyrolesincourses'),
        get_string('isactiv_desc', 'block_overviewmyrolesincourses'),
        1
    ));

    $options = [];
    $systemcontext = \context_system::instance();
    $roles = get_all_roles();
    $rolenames = role_fix_names($roles, $systemcontext, ROLENAME_ORIGINAL);
    $rolescount = count($roles);
    foreach ($rolenames as $rolename) {
        // Only roles that can be assigned on context level course_context.
        $contextlevels = get_role_contextlevels($rolename->id);
        if (in_array(CONTEXT_COURSE, $contextlevels)) {
            $options[$rolename->id] = $rolename->id . " - " .
                $rolename->shortname . " - " .
                $rolename->localname;
        }
    }
    $settings->add(new admin_setting_configmultiselect(
        'block_overviewmyrolesincourses/supportedroles',
        get_string('supportedroles', 'block_overviewmyrolesincourses'),
        get_string('supportedroles_desc', 'block_overviewmyrolesincourses'),
        array_keys($options),
        $options
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_overviewmyrolesincourses/showdeleteicon',
        get_string('showdeleteicon', 'block_overviewmyrolesincourses'),
        get_string('showdeleteicon_desc', 'block_overviewmyrolesincourses'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_overviewmyrolesincourses/defaultshowpast',
        get_string('defaultshowpast', 'block_overviewmyrolesincourses'),
        get_string('defaultshowpast_desc', 'block_overviewmyrolesincourses'),
        1
    ));
    $settings->add(new admin_setting_configcheckbox(
        'block_overviewmyrolesincourses/defaultshowinprogress',
        get_string('defaultshowinprogress', 'block_overviewmyrolesincourses'),
        get_string('defaultshowinprogress_desc', 'block_overviewmyrolesincourses'),
        1
    ));
    $settings->add(new admin_setting_configcheckbox(
        'block_overviewmyrolesincourses/defaultshowfuture',
        get_string('defaultshowfuture', 'block_overviewmyrolesincourses'),
        get_string('defaultshowfuture_desc', 'block_overviewmyrolesincourses'),
        1
    ));
    $settings->add(new admin_setting_configcheckbox(
        'block_overviewmyrolesincourses/defaultfoldonstart',
        get_string('defaultfoldonstart', 'block_overviewmyrolesincourses'),
        get_string('defaultfoldonstart_desc', 'block_overviewmyrolesincourses'),
        0
    ));
    $settings->add(new admin_setting_configcheckbox(
        'block_overviewmyrolesincourses/defaultonlyshowfavourite',
        get_string('defaultonlyshowfavourite', 'block_overviewmyrolesincourses'),
        get_string('defaultonlyshowfavourite_desc', 'block_overviewmyrolesincourses'),
        0
    ));
    $settings->add(new admin_setting_configcheckbox(
        'block_overviewmyrolesincourses/defaultusecategories',
        get_string('defaultusecategories', 'block_overviewmyrolesincourses'),
        get_string('defaultusecategories_desc', 'block_overviewmyrolesincourses'),
        0
    ));
}
