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
 * This block lists the all courses and roles a user is enroled.
 *
 * @package    block_overviewmyrolesincourses
 * @copyright  Andreas Schenkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_overviewmyrolesincourses extends block_base {
    /**
     * @return void
     * @throws coding_exception
     */
    public function init() {
        $this->title = get_string('title', 'block_overviewmyrolesincourses');
    }

    /**
     * @return stdClass|stdObject|string|null
     * @throws coding_exception
     * @throws dml_exception
     */
    public function get_content() {
        // Check if block is activated in websiteadministration plugin settings
        $isactiv = get_config( 'block_overviewmyrolesincourses', 'isactiv');
        if (!get_config( 'block_overviewmyrolesincourses', 'isactiv')) {
            return "isactiv = $isactiv";
        }
        if ($this->content !== null) {
            return $this->content;
        }
        $capabilityviewcontent = has_capability('block/overviewmyrolesincourses:viewcontent', $this->context);
        if (!$capabilityviewcontent) {
            $this->content = null;
            return $this->content;
        }

        global $USER, $OUTPUT;
        // 1. Erstmal alle Kurse in die ein user eingeschrieben ist ermitteln.
        $enroledcourses = enrol_get_my_courses();
        $object = new stdClass();
        if ($enroledcourses) {
            // 2. Die Rollen ermitteln die laut Konfiguration supported sind.
            $supportedroles = get_config('block_overviewmyrolesincourses', 'supportedroles');
            $configuredsupportedroles = explode(',', $supportedroles);
            // 3. Die Rollen ermitteln die im system vorhanden sind.
            $systemcontext = \context_system::instance();
            $rolefixnames = role_fix_names(get_all_roles(), $systemcontext, ROLENAME_ORIGINAL);
            //4. Über alle Rollen itterieren aber nur bei den supporteten Rollen was machen.
            foreach ($rolefixnames as $rolefixname) {
                if (in_array($rolefixname->id, $configuredsupportedroles)) {
                    //5. nur hier uss man alle eingeschriebenen course durchgehen und schauen, ob die rolle dort eingeschriebn ist
                    $shortname = $rolefixname->shortname;
                    $object->$shortname = $this->get_courses_from_enroledcourses_enroled_with_roleid($USER->id, $enroledcourses, $rolefixname->id);
                    $hasrole = "has$shortname";
                    $object->$hasrole = true;
                }
            }
        }
        $objectAsJson = json_encode($object);
        // Seite zusammenbauen und dabei Renderer nutzen
        $this->content = new stdClass;
        $data = $object;
        $this->content->text = $OUTPUT->render_from_template('block_overviewmyrolesincourses/overviewmyrolesincourses', $data);
        $footer = '';
        $this->content->footer = $footer;
        return $this->content;
    }

    /**
     * @param $userid
     * @param $enroledcourses
     * @param $roleid
     * @return array
     */
    public function get_courses_from_enroledcourses_enroled_with_roleid($userid, $enroledcourses, $roleid): array {
        global $OUTPUT;
        $antwort = [];
        foreach ($enroledcourses as $enroledcourse) {
            $coursecontext = context_course::instance($enroledcourse->id);
            if ($enroledcourse->visible == 0 && !has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
                // Only show invisible courses if capability moodle/course:viewhiddencourses.
                continue;
            }
            // Check capability to delete a course
            $showdeleteicon = false;
            if (is_enrolled($coursecontext, $userid, 'moodle/course:delete', $onlyactive = false)) {
                $showdeleteicon = get_config( 'block_overviewmyrolesincourses', 'showdeleteicon');
            }

            $enroledcoursewithrole = new stdClass();
            $userroles = get_user_roles($coursecontext, $userid, true);
            foreach ($userroles as $userrole) {
                if ($userrole->roleid == $roleid) {
                    $visibility = $enroledcourse->visible ? '' : 'dimmed';

                    $enroledcoursewithrole->roleid = $roleid;
                    $enroledcoursewithrole->shortname = $userrole->shortname;

                    // Add all needed courseinformations
                    $enroledcoursewithrole->kursid = $enroledcourse->id;
                    $enroledcoursewithrole->kursname = $enroledcourse->shortname;
                    $enroledcoursewithrole->visible = $enroledcourse->visible;

                    // Add additionalInformation like url to the course, ...
                    $url = "$CFG->wwwroot/course/view.php?id=$enroledcourse->id";
                    $urldelete = "$CFG->wwwroot/course/delete.php?id=$enroledcourse->id";
                    $enroledcoursewithrole->url =$url;
                    $enroledcoursewithrole->visibility = $visibility;
                    $enroledcoursewithrole->duration = $this->createduration($enroledcourse)->duration;
                    $enroledcoursewithrole->durationstatusofcourse = $this->createduration($enroledcourse)->durationstatusofcourse;
                    $enroledcoursewithrole->showdeleteicon = $showdeleteicon;
                    $enroledcoursewithrole->urldelete =$urldelete;

                    $antwort[] = $enroledcoursewithrole;
                }
            }
        }
        return $antwort;
    }

    /**
     * @return bool
     */
    function has_config() {
        return true;
    }



    private function createduration($course): stdClass {
        global $DB;
        $now = time();
        $startdate = date('d/m/Y', $course->startdate);

        // Code: course->enddate is empty if function enrol_get_my_courses() was used.
        $courserecord = $DB->get_record('course', array('id' => $course->id));
        if ($courserecord->enddate) {
            $enddate = date('d/m/Y', $courserecord->enddate);
        } else {
            $enddate = get_string('noenddate', 'block_overviewmyrolesincourses') . ' ';
        }

        // Auslagern: @todo in Funktion.
        $coursecss = '';
        // Documentation of code: if ($course->startdate <= $now) {.
        if ($courserecord->startdate <= $now) {
            if ($courserecord->enddate > $now || !$courserecord->enddate) {
                $coursecss = 'overviewmyrolesincourses-courseactiv';
            } else if ($courserecord->enddate < $now) {
                $coursecss = 'overviewmyrolesincourses-coursefinished';
            }
        } else {
            $coursecss = 'overviewmyrolesincourses-coursefuture';
        }
        $dummy = new stdClass();
        $dummy->duration = "$startdate - $enddate";
        $dummy->durationstatusofcourse = $coursecss;
        return $dummy;
    }



}
