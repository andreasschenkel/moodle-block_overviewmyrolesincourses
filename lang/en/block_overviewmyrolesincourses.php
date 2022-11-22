<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     block_overviewmyrolesincourses
 * @category    string
 * @copyright   Andreas Schenkel
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'overviewmyrolesincourses';
$string['title'] = 'Overview my roles in courses';

$string['isactiv'] = 'Activate report';
$string['isactiv_desc'] = 'If activated the block can be used if the user has the needed capabilities.';

$string['isactivforsiteadmin'] = 'Activ for siteadmin';

$string['supportedroles'] = 'Roles to be supported';
$string['supportedroles_desc'] = 'The report contains only the selected roles.';

$string['noenddate'] = 'open';

$string['showdeleteicon'] = 'Show a delete-icon to delete directly from the block.';
$string['showdeleteicon_desc'] = 'If set to true a delete-icon is shown near the coursename in order to be able to delete the course directly from the block (capability moodle/course:delete needed).';

// Langugage strings for capabilitys.
$string['overviewmyrolesincourses:myaddinstance'] = 'myaddinstance';
$string['overviewmyrolesincourses:addinstance'] = 'addinstance';
$string['overviewmyrolesincourses:viewcontent'] = 'viewcontent';
$string['overviewmyrolesincourses:viewinvisible'] = 'viewinvisible';
