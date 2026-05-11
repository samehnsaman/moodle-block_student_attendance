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
 * Moodle App support for block_student_attendance.
 *
 * @package    block_student_attendance
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

$addons = [
    'block_student_attendance' => [
        'handlers' => [
            'studentattendance' => [
                'delegate' => 'CoreBlockDelegate',
                'method' => 'mobile_view',
                'displaydata' => [
                    'title' => 'pluginname',
                    'class' => 'block_student_attendance',
                ],
                'styles' => [
                    'url' => $CFG->wwwroot . '/blocks/student_attendance/mobile.css',
                    'version' => 2026051000,
                ],
            ],
        ],
        'lang' => [
            ['pluginname', 'block_student_attendance'],
            ['no_attendance_data', 'block_student_attendance'],
            ['sessions_recorded', 'block_student_attendance'],
            ['last5', 'block_student_attendance'],
            ['viewattendance', 'block_student_attendance'],
            ['attendanceactivity', 'block_student_attendance'],
        ],
    ],
];
