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
 * This block lists the all courses and roles a user is enrolled.
 *
 * @package    block_overviewmyrolesincourses
 * @copyright  Andreas Schenkel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_overviewmyrolesincourses extends block_base {
    /**
     * Initialisation
     *
     * @return void
     * @throws coding_exception
     */
    public function init() {
        $this->title = get_string('title', 'block_overviewmyrolesincourses');
    }

    /**
     * Implements the contentcreation.
     *
     * @return stdClass|stdObject|string|null
     * @throws coding_exception
     * @throws dml_exception
     */
    public function get_content() {
        // Check if block is activated in websiteadministration plugin settings.
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
            // 3. Get all existing roles.
            $systemcontext = \context_system::instance();
            $rolefixnames = role_fix_names(get_all_roles(), $systemcontext, ROLENAME_ORIGINAL);
            // 4. Check for all role it the role is supported and then in which courses the user has this role.
            foreach ($rolefixnames as $rolefixname) {
                if (in_array($rolefixname->id, $configuredsupportedroles)) {
                    // 5. If role is supported then add look in the enrolled courses if the user is enrolled with this role.
                    $shortname = $rolefixname->shortname;
                    $object->$shortname = $this->get_courses_enroled_with_roleid($USER->id, $enroledcourses, $rolefixname->id);
                    $hasrole = "has$shortname";
                    $object->$hasrole = true;
                }
            }
        }
        // To get example-json for mustache uncomment following line of code.
        // This can be uses to get a json-example $objectasjson = json_encode($object);
        // Now render the page.
        $this->content = new stdClass;
        $data = $object;
        $this->content->text = $OUTPUT->render_from_template('block_overviewmyrolesincourses/overviewmyrolesincourses', $data);
        $footer = '';
        $this->content->footer = $footer;
        return $this->content;
    }

    /**
     * Gets all courses a user is enroled with a role indicated by $roleid.
     *
     * @param string $userid id of the user
     * @param array $enroledcourses objects of stdClass of courses a user is enrolled
     * @param string $roleid roleid of the role
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     */
    public function get_courses_enroled_with_roleid($userid, $enroledcourses, $roleid): array {
        $result = [];
        foreach ($enroledcourses as $enroledcourse) {
            $coursecontext = context_course::instance($enroledcourse->id);
            if ($enroledcourse->visible == 0 && !has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
                // Only show invisible courses if capability moodle/course:viewhiddencourses.
                continue;
            }
            // Check capability to delete a course.
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

                    // Add all needed courseinformations.
                    $enroledcoursewithrole->kursid = $enroledcourse->id;
                    $enroledcoursewithrole->kursname = $enroledcourse->shortname;
                    $enroledcoursewithrole->visible = $enroledcourse->visible;

                    // Add additional information like url to the course, ...
                    $url = "$CFG->wwwroot/course/view.php?id=$enroledcourse->id";
                    $urldelete = "$CFG->wwwroot/course/delete.php?id=$enroledcourse->id";
                    $enroledcoursewithrole->url = $url;
                    $enroledcoursewithrole->visibility = $visibility;
                    $enroledcoursewithrole->duration = $this->createduration($enroledcourse)->duration;
                    $enroledcoursewithrole->durationstatusofcourse = $this->createduration($enroledcourse)->durationstatusofcourse;
                    $enroledcoursewithrole->showdeleteicon = $showdeleteicon;
                    $enroledcoursewithrole->urldelete = $urldelete;

                    $result[] = $enroledcoursewithrole;
                }
            }
        }
        return $result;
    }

    /**
     * In order to get the settingspage of the plugin in websiteadministration has_config() hast to return true.
     *
     * @return bool
     */
    public function has_config() {
        return true;
    }

    /**
     * Evaluates the start and enddate in order to return this period as a string and the css-code to
     * be uses for already finished courses, just actual usabel courses and courses that will start in the future.
     *
     * @param stdClass $course we are looking for duration information.
     * @return stdClass an object contains the duration as string and css code for the status
     * @throws coding_exception
     * @throws dml_exception
     */
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
        $result = new stdClass();
        $result->duration = "$startdate - $enddate";
        $result->durationstatusofcourse = $coursecss;
        return $result;
    }
}
