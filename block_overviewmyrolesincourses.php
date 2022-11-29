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
    /** Status for courses that ended already */
    const DURATIONSTATUS_PAST = 1;
    /** Status for courses that are in progress */
    const DURATIONSTATUS_INPROGRESS = 2;
    /** Status for courses that start in the future */
    const DURATIONSTATUS_FUTURE = 3;

    /**
     * Initialization
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
        if (!get_config('block_overviewmyrolesincourses', 'isactiv')) {
            // Plugin is installed but is not activated.
            return "";
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
        // 1. Find all courses a user is enrolled.
        $enroledcourses = enrol_get_my_courses();
        $text = '';
        if ($enroledcourses) {
            // 2. Find all roles that the admin has configured as supported roles for this block.
            $supportedroles = get_config('block_overviewmyrolesincourses', 'supportedroles');
            $configuredsupportedroles = explode(',', $supportedroles);
            // 3. Get all existing roles.
            $systemcontext = \context_system::instance();
            $rolefixnames = role_fix_names(get_all_roles(), $systemcontext, ROLENAME_ORIGINAL);
            // 4. Check for every role if the role is supported and then in which courses the user has this role.

            foreach ($rolefixnames as $rolefixname) {
                $data = new stdClass();
                if (in_array($rolefixname->id, $configuredsupportedroles)) {
                    // 5. If role is supported then add look in the enrolled courses if the user is enrolled with this role.
                    $data->roleshortname = $rolefixname->shortname;
                    $data->rolelocalname = $rolefixname->localname;
                    $data->mylist = $this->get_courses_enroled_with_roleid($USER->id, $enroledcourses, $rolefixname->id);
                    // To get example-json for mustache uncomment following line of code.
                    // This can be uses to get a json-example $objectasjson = json_encode($data);
                    // Now render the content for this role and concatenate it with the previous rendered content.
                    if (count($data->mylist) > 0) {
                        $text .= $OUTPUT->render_from_template('block_overviewmyrolesincourses/overviewmyrolesincourses', $data);
                    }
                }
            }
            $text .= $this->create_agenda();
        }

        $this->content = new stdClass;
        $this->content->text = $text;
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
    public function get_courses_enroled_with_roleid(string $userid, array $enroledcourses, string $roleid): array {
        $result = [];
        foreach ($enroledcourses as $enroledcourse) {
            $coursecontext = context_course::instance($enroledcourse->id);
            if ($enroledcourse->visible == 0 && !has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
                // Only show invisible courses if capability moodle/course:viewhiddencourses.
                continue;
            }
            $showpast = $this->config->showpast;
            $showinprogress = $this->config->showinprogress;
            $showfuture = $this->config->showfuture;
            switch ($this->create_duration($enroledcourse)->durationstatus) {
                case self::DURATIONSTATUS_PAST:
                    if ($showpast != '1') {
                        continue 2;
                    }
                    break;
                case self::DURATIONSTATUS_INPROGRESS:
                    if ($showinprogress != '1') {
                        continue 2;
                    }
                    break;
                case self::DURATIONSTATUS_FUTURE:
                    if ($showfuture != '1') {
                        continue 2;
                    }
                    break;
            }

            // Check capability to delete a course.
            $showdeleteicon = false;
            if (is_enrolled($coursecontext, $userid, 'moodle/course:delete', $onlyactive = false)) {
                $showdeleteicon = get_config('block_overviewmyrolesincourses', 'showdeleteicon');
            }

            $enroledcoursewithrole = new stdClass();
            $userroles = get_user_roles($coursecontext, $userid, true);
            foreach ($userroles as $userrole) {
                if ($userrole->roleid == $roleid) {
                    $dimmed = $enroledcourse->visible ? '' : 'dimmed';

                    $enroledcoursewithrole->roleid = $roleid;
                    $enroledcoursewithrole->roleshortname = $userrole->shortname;
                    $enroledcoursewithrole->rolename = role_get_name($userrole);

                    // Add all needed courseinformations.
                    $enroledcoursewithrole->courseid = $enroledcourse->id;
                    $enroledcoursewithrole->courseshortname = $enroledcourse->shortname;
                    $enroledcoursewithrole->coursefullname = $enroledcourse->fullname;
                    $enroledcoursewithrole->visible = $enroledcourse->visible;

                    // Add additional information like url to the course, ...
                    $url = new moodle_url('/course/view.php', ['id' => $enroledcourse->id]);
                    $urldelete = new moodle_url('/course/delete.php', ['id' => $enroledcourse->id]);
                    $enroledcoursewithrole->url = $url;
                    $enroledcoursewithrole->urldelete = $urldelete;
                    $enroledcoursewithrole->dimmed = $dimmed;
                    $enroledcoursewithrole->duration = $this->create_duration($enroledcourse)->duration;
                    $enroledcoursewithrole->durationstatus = $this->create_duration($enroledcourse)->durationstatus;

                    $cssselectordurationstatusofcourse = $this->create_duration($enroledcourse)->cssselectordurationstatusofcourse;
                    $enroledcoursewithrole->cssselectordurationstatusofcourse = $cssselectordurationstatusofcourse;
                    $enroledcoursewithrole->showdeleteicon = $showdeleteicon;

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
    private function create_duration(stdClass $course): stdClass {
        global $DB;
        $now = time();
        $startdate = userdate($course->startdate,  get_string('strftimedatefullshort', 'core_langconfig'));

        // Code: course->enddate is empty if function enrol_get_my_courses() was used.
        $courserecord = $DB->get_record('course', ['id' => $course->id]);
        if ($courserecord->enddate) {
            $enddate = userdate($courserecord->enddate, get_string('strftimedatefullshort', 'core_langconfig'));
        } else {
            $enddate = get_string('noenddate', 'block_overviewmyrolesincourses') . ' ';
        }

        $cssselectordurationstatusofcourse = '';
        // Documentation of code: if ($course->startdate <= $now) {.
        if ($courserecord->startdate <= $now) {
            if ($courserecord->enddate > $now || !$courserecord->enddate) {
                $cssselectordurationstatusofcourse = 'overviewmyrolesincourses-courseinprogress';
                $durationstatus = self::DURATIONSTATUS_INPROGRESS;
            } else if ($courserecord->enddate < $now) {
                $cssselectordurationstatusofcourse = 'overviewmyrolesincourses-coursefinished';
                $durationstatus = self::DURATIONSTATUS_PAST;
            }
        } else {
            $cssselectordurationstatusofcourse = 'overviewmyrolesincourses-coursefuture';
            $durationstatus = self::DURATIONSTATUS_FUTURE;
        }
        $result = new stdClass();
        $result->duration = "$startdate - $enddate";
        $result->cssselectordurationstatusofcourse = $cssselectordurationstatusofcourse;
        $result->durationstatus = $durationstatus;
        return $result;
    }

    /**
     * Generates the html code to explain the used colors for past, in progress and courses that start in the future.
     *
     * @return string the htmlcode with the explanation of the colors
     * @throws coding_exception
     */
    public function create_agenda(): string {
        $agenda = "";
        if ($this->config->showpast) {
            $agenda .= '<div class="row" style="margin-left: 3px;">' .
                '<div class="overviewmyrolesincourses-coursefinished" style="width: 95px;">' .
                    get_string('past', 'block_overviewmyrolesincourses') .
                '</div>' .

                '<div class="overviewmyrolesincourses-coursefinished dimmed" style="width: 110px;">' .
                    get_string('butnotvisible', 'block_overviewmyrolesincourses') .
                '</div>' .
            '</div>';
        }

        if ($this->config->showinprogress) {
            $agenda .= '<div class="row" style="margin-left: 3px;">' .
                '<div class="overviewmyrolesincourses-courseinprogress" style="width: 95px;">' .
                   get_string('inprogress', 'block_overviewmyrolesincourses') .
                '</div>' .
                '<div class="overviewmyrolesincourses-courseinprogress dimmed" style="width: 110px;">' .
                    get_string('butnotvisible', 'block_overviewmyrolesincourses') .
                '</div>' .
            '</div>';
        }

        if ($this->config->showfuture) {
            $agenda .= '<div class="row" style="margin-left: 3px;">' .
                '<div class="overviewmyrolesincourses-coursefuture" style="width: 95px;">' .
                    get_string('future', 'block_overviewmyrolesincourses') .
                '</div>' .
                '<div class="overviewmyrolesincourses-coursefuture dimmed" style="width: 110px;">' .
                    get_string('butnotvisible', 'block_overviewmyrolesincourses') .
                '</div>' .
            '</div>';
        }
        return $agenda;
    }

    /**
     * Store the default-settings the admin has configured when adding the block.
     *
     * @return boolean
     */
    public function instance_create() {
        $data = array(
            'showpast' => get_config('block_overviewmyrolesincourses', 'defaultshowpast'),
            'showinprogress' => get_config('block_overviewmyrolesincourses', 'defaultshowinprogress'),
            'showfuture' => get_config('block_overviewmyrolesincourses', 'defaultshowfuture')
        );
        $this->instance_config_save($data);
        return true;
    }
}
