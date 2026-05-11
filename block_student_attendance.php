<?php

declare(strict_types=1);

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
 * Student attendance block.
 *
 * @package    block_student_attendance
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Displays the current student's attendance records.
 */
class block_student_attendance extends block_base {

    /**
     * Initialises the block title.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_student_attendance');
    }

    /**
     * Allows the block in standard Moodle page areas.
     *
     * @return array
     */
    public function applicable_formats() {
        return [
            'all' => true,
            'my' => true,
            'course-view' => true,
        ];
    }

    /**
     * Allows multiple instances if an administrator wants different placements.
     *
     * @return bool
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     * Gets rendered block content.
     *
     * @return stdClass
     */
    public function get_content() {
        global $OUTPUT, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->footer = '';

        if (!isloggedin() || isguestuser()) {
            $this->content->text = '';
            return $this->content;
        }

        $cards = \block_student_attendance\attendance_data::get_cards((int) $USER->id);
        $data = [
            'uniqid' => html_writer::random_id('block-student-attendance-'),
            'hascards' => !empty($cards),
            'cards' => $cards,
            'emptytext' => get_string('no_attendance_data', 'block_student_attendance'),
            'strings' => [
                'sessionsrecorded' => get_string('sessions_recorded', 'block_student_attendance'),
                'lastrecords' => get_string('last5', 'block_student_attendance'),
                'viewattendance' => get_string('viewattendance', 'block_student_attendance'),
                'activity' => get_string('attendanceactivity', 'block_student_attendance'),
            ],
        ];

        $this->content->text = $OUTPUT->render_from_template('block_student_attendance/carousel', $data);
        return $this->content;
    }

}
